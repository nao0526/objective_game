<?php 

ini_set('log_errors','on');  //ログを取るか
ini_set('error_log','php.log');  //ログの出力ファイルを指定
session_start(); //セッション使う

define('RECOVERY', 100);

$partner = array();
$enemy = array();

$partner[] = array(
    'name' => 'きつねこ',
    'hp' => '300',
    'img' => 'img/partner1-2.png',
    'attack' => mt_rand(30, 60),
);
$partner[] = array(
    'name' => 'こどら',
    'hp' => '300',
    'img' => 'img/partner2-2.png',
    'attack' => mt_rand(30, 60),
);
$partner[] = array(
    'name' => 'うまこーん',
    'hp' => '300',
    'img' => 'img/partner3-2.png',
    'attack' => mt_rand(30, 60),
);

$enemy[] = array(
    'name' => 'おーくまん',
    'hp' => '300',
    'img' => 'img/monster1.png',
    'attack' => mt_rand(30, 60),
);
$enemy[] = array(
    'name' => 'たいちょう',
    'hp' => '300',
    'img' => 'img/monster2.png',
    'attack' => mt_rand(30, 60),
);
$enemy[] = array(
    'name' => 'でぃあんぬ',
    'hp' => '300',
    'img' => 'img/monster3.png',
    'attack' => mt_rand(20, 60),
);
$enemy[] = array(
    'name' => 'ぞんびーむ',
    'hp' => '300',
    'img' => 'img/monster4.png',
    'attack' => mt_rand(30, 50),
);
$enemy[] = array(
    'name' => 'しんば',
    'hp' => '300',
    'img' => 'img/monster5.png',
    'attack' => mt_rand(40, 60),
);
$enemy[] = array(
    'name' => 'たつたのすけ',
    'hp' => '300',
    'img' => 'img/monster6.png',
    'attack' => mt_rand(20, 40),
);
$enemy[] = array(
    'name' => 'どっすん',
    'hp' => '300',
    'img' => 'img/monster7.png',
    'attack' => mt_rand(30, 40),
);
$enemy[] = array(
    'name' => 'むらさきおじさん',
    'hp' => '300',
    'img' => 'img/monster8.png',
    'attack' => mt_rand(20, 40),
);

function createEnemy(){
    global $enemy;
    $viewEnemy = $enemy[mt_rand(0, 7)];
    $_SESSION['enemy_name'] = $viewEnemy['name'];
    $_SESSION['enemy_maxhp'] = $viewEnemy['hp'];
    $_SESSION['enemy_hp'] = $viewEnemy['hp'];
    $_SESSION['enemy_img'] = $viewEnemy['img'];
    $_SESSION['enemy_attack'] = $viewEnemy['attack'];
    $_SESSION['history'] .= $_SESSION['enemy_name'].'が現れた！<br>';
}
function createPartner($partnerNumber){
    global $partner;
    switch ($partnerNumber) {
        case '1':
            $viewPartner = $partner[0];
            break;
        case '2':
            $viewPartner = $partner[1];
            break;
        default:
            $viewPartner = $partner[2];
            break;
    }
    $_SESSION['partner_name'] = $viewPartner['name'];
    $_SESSION['partner_maxhp'] = $viewPartner['hp'];
    $_SESSION['partner_hp'] = $viewPartner['hp'];
    $_SESSION['partner_img'] = $viewPartner['img'];
    $_SESSION['partner_attack'] = $viewPartner['attack'];
    $_SESSION['history'] .= $_SESSION['enemy_name'].'が現れた！<br>';
}
function init($partnerNumber){
    $_SESSION['history'] = '初期化します<br>';
    $_SESSION['knockDownCount'] = 0;
    createEnemy();
    createPartner($partnerNumber);
}
function restart(){
    $_SESSION = array();
}

if(!empty($_POST)){
    $_SESSION['history'] = '';
    $startFlg = (!empty($_POST['start'])) ? true : false;
    $attackFlg = (!empty($_POST['attack'])) ? true : false;
    $recoveryFlg = (!empty($_POST['recovery'])) ? true : false;
    $escapeFlg = (!empty($_POST['escape'])) ? true : false;
    $partnerNumber = (!empty($_POST['partner'])) ? $_POST['partner'] : '';
    if($startFlg){ //キミに決めた！ボタンが押された場合
        init($partnerNumber);
    }else{ // それ以外
        if($attackFlg){
            $_SESSION['history'] .= $_SESSION['partner_name'].'の攻撃<br>';
            $_SESSION['enemy_hp'] -= $_SESSION['partner_attack'];
            $_SESSION['history'] .= $_SESSION['enemy_name'].'は'.$_SESSION['partner_attack'].'ダメージを受けた<br>';
            if($_SESSION['enemy_hp'] <= 0){
                $_SESSION['history'] .= $_SESSION['enemy_name'].'を倒した!<br>';
                $_SESSION['knockDownCount']++;
                createEnemy();
            }else{
                $_SESSION['history'] .= $_SESSION['enemy_name'].'の攻撃<br>';
                $_SESSION['partner_hp'] -= $_SESSION['enemy_attack'];
                $_SESSION['history'] .= $_SESSION['partner_name'].'は'.$_SESSION['enemy_attack'].'ダメージを受けた<br>';
            }
        }elseif($recoveryFlg){
            $_SESSION['history'] .= 'きず薬を使った<br>'.$_SESSION['partner_name'].'は';
            if(($_SESSION['partner_hp'] + RECOVERY) <= $_SESSION['partner_maxhp']){
                $_SESSION['history'] .= RECOVERY;
                $_SESSION['partner_hp'] += RECOVERY;
            }else{
                $_SESSION['history'] .= ($_SESSION['partner_maxhp'] - $_SESSION['partner_hp']);
                $_SESSION['partner_hp'] = $_SESSION['partner_maxhp'];
            }

            $_SESSION['history'] .= '回復した<br>'.$_SESSION['enemy_name'].'の攻撃<br>';
            $_SESSION['partner_hp'] -= $_SESSION['enemy_attack'];
            $_SESSION['history'] .= $_SESSION['partner_name'].'は'.$_SESSION['enemy_attack'].'ダメージを受けた<br>';
            
            
        }elseif($escapeFlg){
            $_SESSION['history'] .= 'うまく逃げきれた！<br>';
            createEnemy();
        }else{
            restart();
        }
    }
}




 ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ポケモン勝ち抜きリーグ！</title>
    <link rel="stylesheet" href="pokemon-font-1.8.1/css/pokemon-font.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <section id="main" class="site-width">
        <!-- 初期画面 -->
        <?php if(empty($_SESSION)): ?>
        <h1>ポ◯モン勝ち抜きリーグ！</h1>
        <h2>パートナーを選んでね！</h2>
        <form class="container" method="post" action="">
            <div class="partner-group js-toggle-active">
                <input id="partner1" type="radio" name="partner" value="1">
                <label for="partner1">
                    <img src="img/partner1-1.png" alt="">
                </label>
            </div>
            <div class="partner-group js-toggle-active">
                <input id="partner2" type="radio" name="partner" value="2">
                <label for="partner2">
                    <img src="img/partner2-1.png" alt="">
                </label>
            </div>
            <div class="partner-group js-toggle-active">
                <input id="partner3" type="radio" name="partner" value="3">
                <label for="partner3">
                    <img src="img/partner3-1.png" alt="">
                </label>
            </div>
            <div class="btn-container">
                <input class="btn" type="submit" name="start" value="▶︎キミにきめた！">
            </div>
        </form>
         <!-- バトル画面 -->
        <?php elseif(!empty($_SESSION) && $_SESSION['partner_hp'] > 0): ?>
        <div class="container enemy-container">
            <div class="status enemy-status">
                <p><?php echo $_SESSION['enemy_name']; ?></p>
                HP <meter value="<?php echo $_SESSION['enemy_hp']; ?>" min="0" max="<?php echo $_SESSION['enemy_maxhp']; ?>" low="<?php echo ($_SESSION['enemy_maxhp'] / 6); ?>" high="<?php echo ($_SESSION['enemy_maxhp']) / 3; ?>"></meter>   
            </div>
            <div class="battle-img">
                <img src="<?php echo $_SESSION['enemy_img']; ?>" alt="">
            </div>
        </div>
        <div class="container partner-container">
            <div class="partner-info">
                <div class="battle-img">
                    <img src="<?php echo $_SESSION['partner_img']; ?>" alt="">
                </div>
                <div class="status partner-status">
                    <p><?php echo $_SESSION['partner_name']; ?></p>
                    HP <meter value="<?php echo $_SESSION['partner_hp']; ?>" min="0" max="<?php echo $_SESSION['partner_maxhp']; ?>" low="<?php echo ($_SESSION['partner_maxhp'] / 6); ?>" high="<?php echo ($_SESSION['partner_maxhp'] / 3); ?>"></meter><br>
                    <span><?php echo $_SESSION['partner_hp']; ?>/<?php echo $_SESSION['partner_maxhp']; ?></span>
                </div>
            </div>
            <p style="text-align: right;">倒したモンスター数 ： <?php echo $_SESSION['knockDownCount']; ?>ひき</p>
            <div class="historyAndAction">
                <div class="history">
                    <p class="history_text"><?php echo $_SESSION['history']; ?></p>
                </div>
                <form class="choice-action" method="post">
                    <div class="btn-container">
                        <span>▶︎</span>
                        <input class="js-choice-action btn" type="submit" name="attack" value="攻撃">
                    </div>
                    <div class="btn-container">
                        <span>▶︎</span>
                        <input class="js-choice-action btn" type="submit" name="recovery" value="きず薬">
                    </div>
                    <div class="btn-container">
                        <span>▶︎</span>
                        <input class="js-choice-action btn" type="submit" name="escape" value="逃げる">
                    </div>
                    <div class="btn-container">
                        <span>▶︎</span>
                        <input class="js-choice-action btn" type="submit" name="restart" value="やり直す">
                    </div>
                    
                </form>
            </div>
        </div>
<?php else: ?>
     <!-- ゲームオーバー画面 -->
        <h1>GAME OVER</h1>
        <p class="defeat-number">倒したモンスター数 ： <?php echo $_SESSION['knockDownCount']; ?>ひき</p>
        <form class="btn-container" method="post">
            <input class="btn" type="submit" name="restart" value="やり直す"">
        </form>
<?php endif; ?>

    </section>
    <script
  src="https://code.jquery.com/jquery-3.4.1.js"
  integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
  crossorigin="anonymous"></script>
  <script src="app.js"></script>
</body>
</html>