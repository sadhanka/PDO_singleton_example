<?php
/**
 * Created by PhpStorm.
 * User: Lyubov Zhurba
 * Date: 19.07.2017
 * Time: 13:26
 */

$output = '';
spl_autoload_register( 'my_autoload_register' );
function my_autoload_register( $class_name ) {
    $file_name = $class_name . '.php';
    if( file_exists( $file_name ) ) {
        require $file_name;
    }
}
ConfigRegistry::set('config.ini');
$db = PDOSingle::getInstance();

if (isset($_GET['new_msg'])) {
    /**
     * do not forget:
     * ALTER TABLE *mytable* ENGINE=InnoDB;
     * ALTER TABLE *mytable* ADD UNIQUE(*id*)
     * ALTER TABLE *mytable* AUTO_INCREMENT=100;
     */
    try {
        // create new user
        $rndNumber = random_int(0, 1000);
        $userQuery = "INSERT INTO user (uname, upass, regdate, lastlogin) VALUES ('user_$rndNumber', 'e10adc3949ba59abbe56e057f20f883e', NOW(), NOW())";
        $db->exec($userQuery);

        // use prepared statements combined with Transactions
        // Insert message with just added user ID
        $newUserID = $db->lastInsertId();
        $msgQuery = "INSERT INTO messages (mtext, uid, mdate, published) VALUES ('PDO transaction', ?, NOW(), 0)";
        $stmtMsg = $db->prepare($msgQuery);
        $stmtMsg->execute( array($newUserID) );

        $db->commit();
        $output = 'Transaction is OK';
    }
    catch (Exception $e) {
        $db->rollBack();
        $output = 'Error: ' . $e->getMessage();
    }
}

//TODO: select uname from user table

//select published or unpublished messages of one user
$dbQuery = 'SELECT * FROM messages WHERE uid = ? and published=?';
$stmt = $db->prepare($dbQuery);

$uid = isset($_GET['uid']) ? $_GET['uid'] : 1;
$publ = isset($_GET['publ']) ? $_GET['publ'] : 1;
$stmt->execute([$uid, $publ]);

$mesagesArray = [];
$mesagesArray = $stmt->fetchAll(PDO::FETCH_ASSOC);

//TODO: create MessageClass and add MessageClass obj here

$output .= '<div class="row"><div class="col-sm-12"><a href="index.php?new_msg=1">Create new user and message</a></div></div>';
$output .= '<div class="row"><div class="col-sm-4"><a href="index.php?uid=1">User ID: 1</a></div>';
$output .= '<div class="col-sm-4"><a href="index.php?uid=2">User ID: 2</a></div>';
$output .= '<div class="col-sm-4"><a href="index.php?uid=3">User ID: 3</a></div></div>';

foreach ($mesagesArray as $row) {
    $output .= '
<div class="row">
    <div class="col-sm-1">User ID: ' . $row['uid'] . '</div>
    <div class="col-sm-2">' . $row['mdate'] . '</div>
    <div class="col-sm-7">' . $row['mtext'] . '</div>
    <div class="col-sm-2"><a href="index.php?uid=' . $row['uid'] . '&publ=' . ($publ ? '0' : '1') . '">' . ($publ ? 'Published' : 'Unpublished') . '</a></div>
</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Guest book</title>

    <link rel="stylesheet" type="text/css" media="screen" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="index.php">Site</a>
        </div>
    </div>
</nav>
<div class="bs-header">
    <div class="container">
        <h3 align="center">Contact us</h3>
    </div>
</div>
<div class="container">
    <div><?=$output;?></div>
    <form class="form-horizontal" action="<?=basename(__FILE__)?>" method="GET" data-toggle="validator" id="form1">
        <div class="form-group">
            <label class="control-label col-sm-4" for="text">Leave a message:</label>
            <div class="col-sm-4">
                <textarea name="text"></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4" for="text">Your email:</label>
            <div class="col-sm-4">
                <input type="text" name="mail" placeholder="example@example.com">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-4">
                <button type="submit" value="Submit" name="submit" class="btn btn-default btn-success">Submit</button>
                <button type="reset" value="Reset" class="btn btn-default">Reset</button>
            </div>
        </div>
    </form>
</div>
</body>
</html>