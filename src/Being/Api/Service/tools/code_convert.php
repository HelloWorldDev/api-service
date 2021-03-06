<?php

// Copy Go Error Code to here, then run "php go_code_convert_to_php.php"

$content = <<<EOF
const (
    CodeSuccess = 0
    CodeInvalidParam = 10400
    CodeRequestTimeout = 10401
    CodeErrorCodeNotExists = 10402
    CodeAuthForbidden = 10403
    CodeSystemError = 10500
    CodeEmptyBody = 10501
    CodeUsernameExists = 10600
    CodeUsernameLength = 10601
    CodeUsernameFormat = 10602
    CodeEmailExists = 10603
    CodeEmailLength = 10604
    CodeEmailFormat = 10605
    CodeFullnameExists = 10606
    CodeFullnameLength = 10607
    CodeFullnameFormat = 10608
    CodePasswordLength = 10609
    CodePasswordFormat = 10610
    CodeUsernameNotExists = 10611
    CodeEmailNotExists = 10612
    CodePasswordNotMatch = 10613
    CodeUserNotExists = 10614
    CodeUnknownThirdParty = 10615
    CodeUsernamePasswordNotMatch = 10616
    CodeEmailPasswordNotMatch = 10617
    CodeMobilePasswordNotMatch = 10618
    CodeVerifyCodeNotMatch   = 10619
    CodeRechargeFailed    = 10620
    CodeUserMoneyNotEnough = 10621
)
EOF;


preg_match_all('/Code([a-zA-Z]+)\s*=\s*(\d+)/', $content, $m);

$goCodes = [];
foreach ($m[2] as $i => $code) {
    $goCodes[0][] = camel2snack($m[1][$i]);
    $goCodes[1][] = $code;
}

$phpCodeFile = __DIR__ . '/../Code.php';
$contentPHP = file_get_contents($phpCodeFile);
preg_match_all('/const\s+([a-zA-Z_]+)\s*=\s*(\d+)/', $contentPHP, $m);

$phpCodes = [
    0 => $m[1],
    1 => $m[2],
];

foreach ($goCodes[0] as $i => $name) {
    $code = $goCodes[1][$i];
    $phpCode = get_php_code_by_name($phpCodes, $name);
    if ($phpCode < 0) {
        $phpCodes[0][] = $name;
        $phpCodes[1][] = $code;
    } elseif ($code != $phpCode) {
        echo "error: go code:$code php code:$phpCode\n";
    }
}

function camel2snack($word)
{
    $words = [];
    for ($i = 0, $l = strlen($word); $i < $l; $i++) {
        if ($i != 0 && ord($word[$i]) < 97) {
            $words[] = '_';
        }
        $words[] = strtoupper($word[$i]);
    }

    return implode('', $words);
}

function snack2camel($word)
{
    $words = [$word[0]];
    for ($i = 1, $l = strlen($word); $i < $l; $i++) {
        if ($word[$i] == '_') {
            $words[] = $word[++$i];
        } else {
            $words[] = strtolower($word[$i]);
        }
    }

    return implode('', $words);
}

function get_php_code_by_name($phpCodes, $name)
{
    foreach ($phpCodes[0] as $i => $val) {
        if ($name == $val) {
            return $phpCodes[1][$i];
        }
    }

    return -1;
}
