<?php 

ini_set('log_errors','on');  //ログを取るか
ini_set('error_log','php.log');  //ログの出力ファイルを指定
session_start(); //セッション使う

define('RECOVERY_POINT', 100);

// インスタンス格納用変数
$partner = array();
$enemy = array();

// モンスタークラス
abstract class Monster{
    protected $name;
    protected $hp;
    protected $img;
    protected $attack;
    
    public function __construct($name, $hp, $img, $attack){
        $this->name = $name;
        $this->hp = $hp;
        $this->img = $img;
        $this->attack = $attack;
    }
    // 攻撃用メソッド
    abstract public function attack($targetObj);
    // セッター
    public function setHp($num){
        $this->hp = filter_var($num, FILTER_VALIDATE_INT);
    }
    // ゲッター
    public function getName(){
        return $this->name;
    }
    public function getHp(){
        return $this->hp;
    }
    public function getImg(){
        if(empty($this->img)){
            return 'img/no-img.png';
        }
        return $this->img;
    }
    public function getAttack(){
        return $this->attack;
    }
}
// パートナー用クラス
class Partner extends Monster{
    private $attackName1;
    private $attackName2;
    private $attackName3;
    private $attackName4;

    public function __construct($name, $hp, $img, $attack, $attackName1, $attackName2, $attackName3, $attackName4){
        parent::__construct($name, $hp, $img, $attack);
        $this->attackName1 = $attackName1;
        $this->attackName2 = $attackName2;
        $this->attackName3 = $attackName3;
        $this->attackName4 = $attackName4;
    }
    public function attack($targetObj){
        $attackPoint = $_SESSION['partner']->getAttack();
        if(!empty($_POST['attack'])){
            $attackName = $_POST['attack'];
        }
        History::set($this->getName().'の'.$attackName.'こうげき');
        switch ($attackName) {
            case $this->attackName1:
                $attackPoint = (int)($attackPoint * 1.2);
                break;
            case $this->attackName2:
                $attackPoint = (int)($attackPoint * 1.5);
                break;
            case $this->attackName3:
                $attackPoint = (int)($attackPoint * 1.7);
                break;
            default:
                $attackPoint = (int)($attackPoint * 2.0);
                break;
        }
        if(!mt_rand(0, 1)){ // 10分の1の確率で攻撃が外れる
            History::set('こうげきはあたらなかった');
        }else{
            if(!mt_rand(0, 9)){ // 10分の1の確率で急所に当てる
                $attackPoint = (int)($attackPoint * 1.5);
                History::set('きゅうしょにあたった!');
            }
            $targetObj->setHp($targetObj->getHp() - $attackPoint);
            History::set($targetObj->getName().'に'.$attackPoint.'ダメージあたえた');
        }
    }
    public function getAttackName(){
        $attackName = array(
            'attackName1' => $this->attackName1,
            'attackName2' => $this->attackName2, 
            'attackName3' => $this->attackName3, 
            'attackName4' => $this->attackName4, 
        );
        return $attackName;
    }
}
// 相手モンスター用クラス
class Enemy extends Monster{

    public function __construct($name, $hp, $img, $attack){
        parent::__construct($name, $hp, $img, $attack);
    }
    public function attack($targetObj){
        $attackPoint = $this->getAttack();
        History::set($this->getName().'のこうげき');
        if(!mt_rand(0, 9)){ // 10分の1の確率で攻撃が外れる
            History::set('こうげきはあたらなかった');
        }else{
            if(!mt_rand(0, 9)){ // 10分の1の確率で急所に当てる
                $attackPoint = (int)($attackPoint * 1.5);
                History::set('きゅうしょにあたった!');
            }
            $targetObj->setHp($targetObj->getHp() - $attackPoint);
            History::set($attackPoint.'ダメージうけた');
        }
    }
}
// 魔法を使う相手モンスター用クラス
class magicEnemy extends Enemy{
    private $magicAttack;
    public function __construct($name, $hp, $img, $attack, $magicAttack){
        parent::__construct($name, $hp, $img, $attack);
        $this->magicAttack = $magicAttack;
    }
    public function attack($targetObj){
        $attackPoint = $this->getMagicAttack();
        if(!mt_rand(0, 4)){ // 5分の1の確率で魔法攻撃をする
            History::set($_SESSION['enemy']->getName().'のまほうこうげき');
            if(!mt_rand(0, 9)){ // 10分の1の確率で攻撃が外れる
                History::set('こうげきはあたらなかった');
            }else{
                if(!mt_rand(0, 9)){ // 10分の1の確率で急所に当てる
                    $attackPoint = (int)($attackPoint * 1.5);
                    History::set('きゅうしょにあたった!');
                }
                $targetObj->setHp($targetObj->getHp() - $attackPoint);
                History::set($attackPoint.'ダメージうけた');
            }
        }else{
            parent::attack($targetObj);
        }
    }
    public function getMagicAttack(){
        return $this->magicAttack;
    }
}
interface HistoryInterface{
    public static function set($str);
    public static function clear();
}
// 履歴管理用クラス
class History implements HistoryInterface{
    // セッター
    public static function set($str){
        // もしセッションhistoryが生成されていなかったら生成する
        if(empty($_SESSION['history'])){
            $_SESSION['history'] = '';
        }
        // 文字列をセッションに格納
        $_SESSION['history'] .= $str.'<br>';
    }
    // セッションhistoryを空にする
    public static function clear(){
        unset($_SESSION['history']);
    }
}
// パートナーインスタンス生成
$partner[] = new Partner('きつねこ', '300', 'img/partner1-2.png', mt_rand(30, 60), 'たいあたり', 'みだれひっかき', 'ねんりき', 'サイコキネシス');
$partner[] = new Partner('こどら', '300', 'img/partner2-2.png', mt_rand(30, 60), 'たいあたり', 'ひのこ', 'かえんほうしゃ', 'オーバーヒート');
$partner[] = new Partner('うまこーん', '300', 'img/partner3-2.png', mt_rand(30, 60), 'たいあたり', 'うしろげり', 'とっしん', 'うまびーむ');
// 相手モンスターインスタンス生成
$enemy[] = new Enemy('おーくまん', '300', 'img/monster1.png', mt_rand(30, 60));
$enemy[] = new magicEnemy('たいちょう', '300', 'img/monster2.png', mt_rand(30, 60), mt_rand(40, 80));
$enemy[] = new Enemy('でぃあんぬ', '300', 'img/monster3.png', mt_rand(20, 60));
$enemy[] = new magicEnemy('ぞんびーむ', '300', 'img/monster4.png', mt_rand(30, 50), mt_rand(50, 70));
$enemy[] = new Enemy('はくなまたた', '300', 'img/monster5.png', mt_rand(40,  60));
$enemy[] = new magicEnemy('たつたのすけ', '300', 'img/monster6.png', mt_rand(20, 40), mt_rand(30, 70));
$enemy[] = new Enemy('どっすん', '300', 'img/monster7.png', mt_rand(30, 40));
$enemy[] = new magicEnemy('むらさきおじさん', '300', 'img/monster8.png', mt_rand(20, 40), mt_rand(40, 80));
// 相手モンスター生成関数
function createEnemy(){
    global $enemy;
    $_SESSION['enemy'] = $enemy[mt_rand(0, 7)];
    $_SESSION['enemy_maxhp'] = $_SESSION['enemy']->getHp();
    History::set('あ!');
    History::set('やせいの'.$_SESSION['enemy']->getName().'があらわれた!');
}
// パートナー生成関数
function createPartner(){
    global $partner;
    // もしパートナーを選ばすにスタートされたら、パートナーをランダムに生成する
    $partnerNumber = (!empty($_POST['partner'])) ? $_POST['partner'] : mt_rand(0, 2);
    switch ($partnerNumber) {
        case '1':
            $_SESSION['partner'] = $partner[0];
            break;
        case '2':
            $_SESSION['partner'] = $partner[1];
            break;
        default:
            $_SESSION['partner'] = $partner[2];
            break;
    }
    $_SESSION['partner_maxhp'] = $_SESSION['partner']->getHp();
}
// 初期化用関数
function init(){
    History::clear();
    $_SESSION['knockDownCount'] = 0;
    createEnemy();
    createPartner();
}
// リスタート用関数（セッション初期化用）
function restart(){
    $_SESSION = array();
}
// POST送信があった場合
if(!empty($_POST)){
    $startFlg = (!empty($_POST['start'])) ? true : false;
    $attackFlg = (!empty($_POST['attack'])) ? true : false;
    $recoveryFlg = (!empty($_POST['recovery'])) ? true : false;
    $escapeFlg = (!empty($_POST['escape'])) ? true : false;
    if($startFlg){ //キミに決めた！ボタンが押された場合
        init();
    }else{ // それ以外
        History::clear();
        if($attackFlg){ // 攻撃だった場合
            // 相手に攻撃を与える
            $_SESSION['partner']->attack($_SESSION['enemy']);
            if($_SESSION['enemy']->getHp() <= 0){ // 相手のHPが0以下になったら次のモンスターを生成
                History::set($_SESSION['enemy']->getName().'をたおした!');
                $_SESSION['knockDownCount']++;
                createEnemy();
            }else{ // 相手のHPが0以上ならば相手が攻撃をする
                $_SESSION['enemy']->attack($_SESSION['partner']);
            }
        }elseif($recoveryFlg){ //きずぐすりボタンが押された場合
            $recoveryPoint = 0;
            History::set('きずぐすりをつかった');
            if(($_SESSION['partner_maxhp'] - $_SESSION['partner']->getHp() >= RECOVERY_POINT)){ // HPのMAX値と現在のHPの差がきずぐすりの回復力以上だった場合
                $recoveryPoint = RECOVERY_POINT;
                $_SESSION['partner']->setHp($_SESSION['partner']->getHp() + RECOVERY_POINT);
            }else{ // HPのMAX値と現在のHPの差がきずぐすりの回復力未満だった場合
                $recoveryPoint = ($_SESSION['partner_maxhp'] - $_SESSION['partner']->getHp());
                $_SESSION['partner']->setHp($_SESSION['partner_maxhp']);
            }
            History::set($_SESSION['partner']->getName().'は'.$recoveryPoint.'かいふくした');
            // 相手が攻撃をする
            $_SESSION['enemy']->attack($_SESSION['partner']);
        }elseif($escapeFlg){ //逃げるボタンが押された場合
            History::set('うまくにげきれた!');
            createEnemy();
        }else{ // やりなおすボタンが押された場合
            restart();
        }
    }
    if(!empty($_SESSION['partner'])){ // パートナーのアタックネームを変数に格納
        $attackName = $_SESSION['partner']->getAttackName();
    }
}
 ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>プロモンかちぬきリーグ！</title>
    <link rel="stylesheet" href="pokemon-font-1.8.1/css/pokemon-font.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <section id="main" class="site-width">
        <!-- 初期画面 -->
        <?php if(empty($_SESSION)): ?>
        <h1>プロモンかちぬきリーグ!</h1>
        <h2>すきなプロモンをえらんでね!</h2>
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
        <?php elseif(!empty($_SESSION) && $_SESSION['partner']->getHp() > 0): ?>
        <div class="container enemy-container">
            <div class="status enemy-status">
                <p><?php echo $_SESSION['enemy']->getName(); ?></p>
                HP <meter value="<?php echo $_SESSION['enemy']->getHp(); ?>" min="0" max="<?php echo $_SESSION['enemy_maxhp']; ?>" low="<?php echo ($_SESSION['enemy_maxhp'] / 6); ?>" high="<?php echo ($_SESSION['enemy_maxhp']) / 3; ?>"></meter><br>
                <span><?php echo $_SESSION['enemy']->getHp(); ?>/<?php echo $_SESSION['enemy_maxhp']; ?></span>
            </div>
            <div class="battle-img">
                <img src="<?php echo $_SESSION['enemy']->getImg(); ?>" alt="">
            </div>
        </div>
        <div class="container partner-container">
            <div class="partner-info">
                <div class="battle-img">
                    <img src="<?php echo $_SESSION['partner']->getImg(); ?>" alt="">
                </div>
                <div class="status partner-status">
                    <p><?php echo $_SESSION['partner']->getName(); ?></p>
                    HP <meter value="<?php echo $_SESSION['partner']->getHp(); ?>" min="0" max="<?php echo $_SESSION['partner_maxhp']; ?>" low="<?php echo ($_SESSION['partner_maxhp'] / 6); ?>" high="<?php echo ($_SESSION['partner_maxhp'] / 3); ?>"></meter><br>
                    <span><?php echo $_SESSION['partner']->getHp(); ?>/<?php echo $_SESSION['partner_maxhp']; ?></span>
                </div>
            </div>
            <p  style="text-align: right;">たおしたモンスター ： <?php echo $_SESSION['knockDownCount']; ?>ひき</p>
            <div class="historyAndAction">
                <div class="history">
                    <p class="history_text"><?php echo $_SESSION['history']; ?></p>
                </div>
                <form class="choice-action active js-set-action" method="post">
                    <div class="btn-container">
                        <span>▶︎</span>
                        <input class="js-choice-action js-toggle-action btn" type="submit" name="attack" value="たたかう">
                    </div>
                    <div class="btn-container">
                        <span>▶︎</span>
                        <input class="js-choice-action btn" type="submit" name="recovery" value="きずぐすり">
                    </div>
                    <div class="btn-container">
                        <span>▶︎</span>
                        <input class="js-choice-action btn" type="submit" name="escape" value="にげる">
                    </div>
                    <div class="btn-container">
                        <span>▶︎</span>
                        <input class="js-choice-action btn" type="submit" name="restart" value="やりなおす">
                    </div>
                </form>
                <form class="choice-action js-set-action" method="post">
                    <div class="btn-container">
                        <span>▶︎</span>
                        <input class="js-choice-action btn" type="submit" name="attack" value="<?php echo $attackName['attackName1']; ?>">
                    </div>
                    <div class="btn-container">
                        <span>▶︎</span>
                        <input class="js-choice-action btn" type="submit" name="attack" value="<?php echo $attackName['attackName2']; ?>">
                    </div>
                    <div class="btn-container">
                        <span>▶︎</span>
                        <input class="js-choice-action btn" type="submit" name="attack" value="<?php echo $attackName['attackName3']; ?>">
                    </div>
                    <div class="btn-container">
                        <span>▶︎</span>
                        <input class="js-choice-action btn" type="submit" name="attack" value="<?php echo $attackName['attackName4']; ?>">
                    </div>
                </form>
            </div>
        </div>
        <?php else: ?>
        <!-- ゲームオーバー画面 -->
        <h1>GAME OVER</h1>
        <p class="defeat-number">たおしたモンスター ： <?php echo $_SESSION['knockDownCount']; ?>ひき</p>
        <form class="btn-container" method="post">
            <input class="btn" type="submit" name="restart" value="やりなおす">
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