<?php
/**
 * Startup
 */
require_once 'setup.php';

/**
 * Alias Fidor SDK namespace
 */
use Fidor\SDK as Fidor;

/**
 * Load Fido API settings
 */
$settings = include 'config.php';

/**
 * Create Config instance. Check Config class for alternative calls.
 *
 */
$config = Fidor\Config::fromArray( $settings );

/**
 * Add oauth parameters ( access token, refresh token and expiration time ) to config
 */
$config->setAccessToken( $_SESSION['oauth']['access_token'] )
       ->setRefreshToken( $_SESSION['oauth']['refresh_token'] )
       ->setExpiresIn( $_SESSION['oauth']['expires_in'] + $_SESSION['oauth']['auth_at'] );

/**
 * Check if token has expired and try refreshing it
 */
/*if ( $config->hasTokenExpired() ) {
    $auth = new Fidor\Authorization( $config );
    $resp = $auth->refresh( $_SESSION['oauth']['refresh_token'] );
    print_r( $resp );
    die();
}*/

/**
 * Create API client
 */
$client = new Fidor\Client( $config );

/**
 * Get transactions
 */
$transactions = $client->transactions->get( null, 1, 5 );
if ( ! empty( $transactions['message'] ) ) {
    header( 'Location: error_token_expired.php?error=' . $transactions['message'] );
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>FIDOR API Starter Kit: Transactions</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

    <div class="container">

        <h1>Fidor API Starter Kit: Transactions</h1>

        <p>
            The call &quot;GET /transactions?page=1&amp;per_page=5&quot; retrieved the following data (listing only the last five entries):
        </p>
        <?php foreach ( $transactions as $transaction ): ?>

        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered table-condensed table-hover table-striped col-md-6">
                    <tbody>
                        <?php
                            foreach ( $transaction as $k => $v ) {
                                if ( is_array( $v )) {
                                    continue;
                                }
                                echo '<tr><th scope="row" style="text-align: left;">' . $k . '</th><td>' . $v . '</td></tr>';
                            }
                        ?>
                        <tr>
                            <td colspan="2">
                                <p>transaction_type_details</p>
                                <table class="table table-bordered table-condensed table-hover table-striped">
                                    <tbody>
                                        <?php
                                            foreach ( $transaction['transaction_type_details'] as $k => $v ) {
                                                echo '<tr><th scope="row" style="text-align: left;">' . $k . '</th><td>' . $v . '</td></tr>';
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <?php endforeach; ?>

        <p>You can now try the following: </p>

        <p>
            <a href="get_customers.php" class="btn btn-primary">Retrieve personal data: GET /customers</a>
        </p>

        <p>
            <a href="get_accounts.php" class="btn btn-primary">Retrieve personal data: GET /accounts</a>
        </p>

        <p>
            <a href="get_transactions.php" class="btn btn-primary">Retrieve personal data: GET /transactions</a>
        </p>

        <p>
            <a href="transfer_money.php" class="btn btn-primary">Transfer money...</a>
        </p>

        <p>Documentation reference: </p>
        <ul>
            <li><a href="http://docs.fidor.de/#transactions" target="_blank">http://docs.fidor.de/#transactions</a></li>
        </ul>

    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
