<?php
// fetch currency
$countryPrefix = Utility::getValue($_REQUEST, 'country_prefix');
$countries = new Countries();
$currency = $countries->getCurrency($countryPrefix);


$responseCode = Utility::getValue($result, 'httpResponseCode');
$myPengoResponses = Utility::getValue($result, 'httpResponseMessage', array());


if ($responseCode != 200) {
    exit("Failed to fetch data from mypengo local api.");
}


$responseGroupedByCode = array('200' => array(), '404' => array(), '400' => array());

foreach ($myPengoResponses as $response) {
    $httpMypengoResponseCode = Utility::getValue($response, 'httpMypengoResponseCode');
    $httpMypengoResponseMessage = Utility::getValue($response, 'httpMypengoResponseMessage');
    $responseGroupedByCode[$httpMypengoResponseCode][] = $httpMypengoResponseMessage;
}


//Utility::r($result);
// subscription data...
if (count($responseGroupedByCode[200])) {
    ?>

    <div class="box" style="margin-top: 8px;">
        <div class="tab-header">
            Subscriptions
        </div>
        <div>
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Service Name</th>
                    <th style="text-align: center;">Caretool</th>
                    <th style="text-align: center;">Activated</th>
                    <th style="text-align: center;">Unsubscribed</th>
                    <th style="text-align: center;">Last Bill</th>
                    <th style="text-align: center;">Total Billed Amount</th>
                    <th style="text-align: center;">Subscription Status</th>
                    <th style="text-align: center;"></th>
                </tr>
                <?php
                foreach ($responseGroupedByCode[200] as $subscription) {

                    $activationDate = current(explode('T', Utility::getValue($subscription, 'ActivationDate')));
                    $unsubscriptionDate = current(explode('T', Utility::getValue($subscription, 'UnsubscriptionDate')));
                    $lastSuccessfulBillingAttempt = current(explode('T', Utility::getValue($subscription, 'LastSuccessfulBillingAttempt')));
                    $msisdn = Utility::getValue($subscription, 'Msisdn');
                    $serviceId = Utility::getValue($subscription, 'mappedServiceId');
                    $subscriptionStatusDescription = Utility::getValue($subscription, 'SubscriptionStatusDescription');
                    $totalBilledAmount = Utility::getValue($subscription, 'TotalBilledAmount');
                    $ticketId = Utility::getValue($subscription, 'ticketId');
                    $id = 'btn-' . uniqid();
                    ?>
                    <tr>
                        <td><b><?php echo Utility::getValue($subscription, 'ServiceName'); ?></b></td>
                        <td style="text-align: center;"><?php echo Utility::getValue($subscription, 'caretool', ''); ?></td>
                        <td style="text-align: center;"><?php echo $activationDate; ?></td>
                        <td style="text-align: center;"><?php echo $unsubscriptionDate; ?></td>
                        <td style="text-align: center;"><?php echo $lastSuccessfulBillingAttempt; ?></td>
                        <td style="text-align: center;"><?php echo $currency . ' ' . Utility::round($totalBilledAmount); ?></td>
                        <td style="text-align: center;"><b><?php echo $subscriptionStatusDescription; ?></b></td>
                        <th style="text-align: center;" id="<?php echo $id; ?>">
                            <?php if (trim($subscriptionStatusDescription) == 'Active') { ?>
                                <button class="mini button" onclick="mypengo_unsub_via_api('<?php echo $serviceId; ?>', '<?php echo $id; ?>')">Unsubscribe</button>
                            <?php } ?>
                        </th>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
    </div>
    <?php
    return;
}


//Subscription Not Found
if (count($responseGroupedByCode[404])) {
    ?>
    <div class="box" style="margin-top: 8px;">
        <div class="tab-header">
            Subscriptions
        </div>
        <div class="padded">
            <p>
                <b>Subscription not found !</b>
            </p>
            <p>
                <em style="color: red">Note: Please try fetching the information from caretool.</em>
            </p>

        </div>
    </div>
    <?php
    return;
}

//Invalid Service ID
if (count($responseGroupedByCode[400])) {
    Utility::r($responseGroupedByCode[400]);
    return;
}
?>
