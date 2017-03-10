<?php
namespace Omnipay\JDPay\Utils;

class XMLUtil{
	public static function arrtoxml($arr,$dom=0,$item=0){
		//ksort($arr);
		if (!$dom){
				
			$dom = new \DOMDocument("1.0","UTF-8");
		}
		if(!$item){
			$item = $dom->createElement("jdpay");
			$item = $dom->appendChild($item);
		}
		foreach ($arr as $key=>$val){
			$itemx = $dom->createElement(is_string($key)?$key:"item");
			$itemx = $item->appendChild($itemx);
			if (!is_array($val)){
				$text = $dom->createTextNode($val);
				$text = $itemx->appendChild($text);
				 
			}else {
				XMLUtil::arrtoxml($val,$dom,$itemx);
			}
		}
		return $dom;
	}
	
	public static function xmlToString($dom){
		$xmlStr = $dom->saveXML();
		$xmlStr = str_replace("\r", "", $xmlStr);
		$xmlStr = str_replace("\n", "", $xmlStr);
		$xmlStr = str_replace("\t", "", $xmlStr);
		$xmlStr = preg_replace("/>\s+</", "><", $xmlStr);
		$xmlStr = preg_replace("/\s+\/>/", "/>", $xmlStr);
		$xmlStr = str_replace("=utf-8", "=UTF-8", $xmlStr);
		return $xmlStr;
	}
	
	public static function encryptReqXml($param){
		$dom = XMLUtil::arrtoxml($param);
		$xmlStr = XMLUtil::xmlToString($dom);
		//echo "源串：".htmlspecialchars($xmlStr)."<br/>";
		$sha256SourceSignString = hash("sha256", $xmlStr);
		//echo "摘要:".$sha256SourceSignString."<br/>";
		$sign = RSAUtils::encryptByPrivateKey($sha256SourceSignString);
		$rootDom = $dom->getElementsByTagName("jdpay");
		$signDom = $dom->createElement("sign");
		$signDom = $rootDom[0]->appendChild($signDom);
		$signText = $dom->createTextNode($sign);
		$signText = $signDom->appendChild($signText);
		$data = XMLUtil::xmlToString($dom);
		//echo "封装后:".htmlspecialchars($data)."<br/>";
		
		$desKey = ConfigUtil::get_val_by_key("desKey");
		$keys = base64_decode($desKey);
		$encrypt = TDESUtil::encrypt2HexStr($keys, $data);
		//echo "3DES后:".$encrypt."<br/>";
		$encrypt = base64_encode($encrypt);
		//echo "base64后:".$encrypt."<br/>";
		$reqParam;
		$reqParam["version"]=$param["version"];
		$reqParam["merchant"]=$param["merchant"];
		$reqParam["encrypt"]=$encrypt;
		$reqDom = XMLUtil::arrtoxml($reqParam,0,0);
		$reqXmlStr = XMLUtil::xmlToString($reqDom);
		//echo htmlspecialchars($reqXmlStr)."<br/>";
		return $reqXmlStr;
	}

	public static function decryptResXml($resultData, $desKey, &$resData){
		$resultXml = simplexml_load_string($resultData);
		$resultObj = json_decode(json_encode($resultXml),TRUE);
		$encryptStr = $resultObj["encrypt"];
		$encryptStr=base64_decode($encryptStr);
		$keys = base64_decode($desKey);
		$reqBody = TDESUtil::decrypt4HexStr($keys, $encryptStr);
		$bodyXml = simplexml_load_string($reqBody);
		$resData = json_decode(json_encode($bodyXml),TRUE);
		$inputSign = $resData["sign"];

		$startIndex = strpos($reqBody,"<sign>");
		$endIndex = strpos($reqBody,"</sign>");
		$xml = '';
		if($startIndex!=false && $endIndex!=false){
			$xmls = substr($reqBody, 0,$startIndex);
			$xmle = substr($reqBody,$endIndex+7,strlen($reqBody));
			$xml=$xmls.$xmle;
		}

		$sha256SourceSignString = hash("sha256", $xml);
		$decryptStr = RSAUtils::decryptByPublicKey($inputSign);
		$flag = !empty($decryptStr) && $decryptStr == $sha256SourceSignString;
		$resData["version"]=$resultObj["version"];
		$resData["merchant"]=$resultObj["merchant"];
		$resData["result"]=$resultObj["result"];

		return $flag && $resData['status'] == 2;
	}
}
?>