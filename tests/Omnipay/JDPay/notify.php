<?php

use Omnipay\JDPay\Utils\VerifyUtils;

require __DIR__ . '/common.php';

jdpay_log('notify');
jdpay_log($_POST);
jdpay_log($_GET);
jdpay_log(file_get_contents('php://input'));

$notifyData = <<<EOF
<?xml version="1.0" encoding="UTF-8" ?>
<jdpay>
  <version>V2.0</version>
  <merchant>22294531</merchant>
  <result>
    <code>000000</code>
    <desc>success</desc>
  </result>
  <encrypt>MDNkNTM0Y2FiMzY3YWI3ODhjNjc0YmY1ZWJlY2QyODU0YTc5NmQ3ZWQxMWU1NzE3MWQ0OTUwOGI5NzllYmE4ZjM1YzRiZjlmYWE1M2ZiYjVmYzBmYTgyMDYyM2Q0YjM0NGM1ODFkZDhlYTA2Mjk0ZDE5ZDBlZDk5NTc3MmE4Nzk4OTFlYjIwZDgzMTc4MDU3NGVkZTFjNDY0MDMzNzNjZjc2OWZiMDQ0YjVhZGNhYmRhMGZmYTkyNzRhZDNhM2IxOGY5ZjZhYjBmYjhmZmI3Yzg0OTA3YzM0OGJmZTYwZTIzNzM3YjVmYzMzNmNkYTE0MjM2OWIwZDM5MjI2YWM5YmY3ZmZjZDBkNWJmM2ZkYWY4YTU3OWU4MDE3ZjQ5YmQ0ZWIyMDA0NTFmODZkNmViMGM4N2FjNjc0YjI5YWM2MmUzZDJlYTlkZDVmNTU4MzExMzQ3NWVmMzQyNjMwZDhjZmRlOTM3YTRhYjhlYmZhNzRlYTM2ZmNjMzdhM2EzNTcwNmY0YTFmYTAzMDA1Yjg5MzNkMWJkZTVkNTE2NTlmMDA1YWFmNDY5YzRjMTQ0OWIwYzBhNTZhZWFiOWUyZDQ5OTZkZGE3NTA2Y2VmMDkyYzIxYWU2OTQyMjk2MjBiMTM3OTUyMTQzMjY1YmY1MjA5NTY1MTI1NzBlYzA0M2ZjNDkzODhjMzhhNzgwNWZmZDg3YmEzNTUzNmU1ODgwNTczNTQ3OWQwMjNkNDc4MWQ2NjA0YjMyMDY1NWY2ODZkZjZkMjg3Yzc5YWM5MTdmYWMwY2UwZmZhZjNmN2ZhNGUyZGQ3NzkyZDY4MjZkZDhjZmEwYTcyMTdmNmUwOTM4MjhhZGYxMTFlY2M0M2EwNzkyYjQ1NmFkODQwM2U3YmJhNzFlZDQ5NmE1Y2ZjYTExNmE4YmE4NzRjODI4YjkzMzdmYjA5ZmY0MTFkZjZkMzdjZWQ0ODNmZDMwNWRjY2NlNmU5NWI2NWIxNjcyNWYyMWY4NDg3NGJmOTI0MDY1MTYwOGI4ZmZmZjA2OTVjN2QzOTUzZWJhMDNjNTA5MzM1NzUzOTAzNDdjMzlmYjI2ZjVkNjcyNTk1MzBkNTU1NGNlYjQ2NzNjZDIwZTI1ODRiMTZjZGNlNWRiNzI1MDQwZjk5NTYxNzgzNDg3MTI3ODE2ZTgwZjlhZTE3NjE0ZDIzM2Q2MTQ4YzJiNWE3ZWVjMDU5MjFmZmVhM2Y4YWU0N2E1ZDRlOGQ2MzFjNWRmOGFiMzkyYWE0MjMwYWM1YjQwNWViZjA0MTFmOWVhYTUxZGNkNmE4ODlmMWU3NWIxOTdhN2E2MjRhMzEyYzU3MzM2OTcwZmNjOTM3OGIxZDM1MWJmZWQ3MWJiNTJlOWFjMmExN2I0NGViZTNhODgxYWFjYWI4YzkyNzlkNmZlZTI0M2EyMjIyMTQ1YTEzMGVlYmQ5OGEzMTYyNWE4ZjAxYTZmOWZkYmY5MjBhZTlkOGM4MjAwYjFkOWI3ODM2ODI2ODJmYjY0ODI0ZTM4ZDIwZmU5MTI2ZTY2NWFkODYxOWVmOGYwNzk1YTA3N2VkMjA1YTU1N2Y5ZDM3MzRkMzU4OGQxODI0ZjQ4MTFmOWVhYTUxZGNkNmE4ODlkN2VhMzUxMjRmNGI5NDc=</encrypt>
</jdpay>
EOF;

$flag = VerifyUtils::verifyPurchaseNotify($notifyData, 'ta4E/aspLA3lgFGKmNDNRYU92RkZ4w2t', $data);

print_r($data);
var_dump($flag);
