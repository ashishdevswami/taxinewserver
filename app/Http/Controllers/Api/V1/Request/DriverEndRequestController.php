<?php

namespace App\Http\Controllers\Api\V1\Request;

use App\Jobs\NotifyViaMqtt;
use App\Models\Admin\Promo;
use App\Models\Request\RequestPlace;


use App\Jobs\NotifyViaSocket;
use Kreait\Firebase\Contract\Database;
use Illuminate\Support\Carbon;
use App\Models\Admin\PromoUser;
use App\Base\Constants\Masters\UnitType;
use App\Base\Constants\Masters\PushEnums;
use App\Base\Constants\Masters\PaymentType;
use App\Base\Constants\Masters\WalletRemarks;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Request\DriverEndRequest;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Transformers\Requests\TripRequestTransformer;
use App\Models\Admin\ZoneTypePackagePrice;
use Illuminate\Support\Facades\Log;
use App\Models\Request\RequestCancellationFee;
use App\Base\Constants\Setting\Settings;

/**
 * @group Driver-trips-apis
 *
 * APIs for Driver-trips apis
 */
class DriverEndRequestController extends BaseController
{
    public function __construct(Database $database)
    {
        $this->database = $database;
    }
    /**
    * Driver End Request
    * @bodyParam request_id uuid required id request
    * @bodyParam distance double required distance of request
    * @bodyParam before_trip_start_waiting_time double required before arrival waiting time of request
    * @bodyParam after_trip_start_waiting_time double required after arrival waiting time of request
    * @bodyParam drop_lat double required drop lattitude of request
    * @bodyParam drop_lng double required drop longitude of request
    * @bodyParam drop_address double required drop drop Address of request
    * @responseFile responses/requests/request_bill.json
    *
    */
    public function endRequest(DriverEndRequest $request)
    {
        // Get Request Detail
        $driver = auth()->user()->driver;

        $request_detail = $driver->requestDetail()->where('id', $request->request_id)->first();
        
        $request_places = RequestPlace::where('request_id', $request->request_id)->first();

        if (!$request_detail) {
            $this->throwAuthorizationException();
        }
        
        $user = $request_detail->userDetail;
        
        if (!$request_detail->is_later) {
            $ride_type = 1;
        } else {
            $ride_type = 2;
        }
        $zone_type = $request_detail->zoneType;

        $zone_type_price = $zone_type->zoneTypePrice()->where('price_type', $ride_type)->first();
        
        $drivercommision = $zone_type_price->driver_base_price +  ($request_detail->total_distance - $zone_type_price->driver_base_distance)*$zone_type_price->driver_price_per_distance;
        $drivercommision = number_format($drivercommision,2);
        // Validate Trip request data
        
           // echo "<pre>"; print_r($user);
            //echo "<pre>"; print_r($request_detail); die();
                 //send email
        $taxamount = $request_detail->request_eta_amount*5/100;
        $amountShow = $request_detail->request_eta_amount-$taxamount;
        
            $subject = "Trip Invoice";
            $message = '<html xmlns="http://www.w3.org/1999/xhtml"><head>
                	<meta http-equiv="content-type" content="text/html; charset=utf-8">
                  	<meta name="viewport" content="width=device-width, initial-scale=1.0;">
                 	<meta name="format-detection" content="telephone=no"/>
                	<style>
                body { margin: 0; padding: 0; min-width: 100%; width: 100% !important; height: 100% !important;}
                body, table, td, div, p, a { -webkit-font-smoothing: antialiased; text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; line-height: 100%; }
                table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse !important; border-spacing: 0; }
                img { border: 0; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
                #outlook a { padding: 0; }
                .ReadMsgBody { width: 100%; } .ExternalClass { width: 100%; }
                .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
                @media all and (min-width: 560px) {
                	.container { border-radius: 8px; -webkit-border-radius: 8px; -moz-border-radius: 8px; -khtml-border-radius: 8px;}
                }
                a, a:hover {
                	color: #127DB3;
                }
                .footer a, .footer a:hover {
                	color: #999999;
                }
                 	</style>
                	<title>Invoice Template</title>
                </head>
                <body topmargin="0" rightmargin="0" bottommargin="0" leftmargin="0" marginwidth="0" marginheight="0" width="100%" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 100%; height: 100%; -webkit-font-smoothing: antialiased; text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; line-height: 100%;
                	background-color: #F0F0F0;
                	color: #000000;"
                	bgcolor="#F0F0F0"
                	text="#000000">
                <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 100%;" class="background"><tr><td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;"
                	bgcolor="#F0F0F0">
                <table border="0" cellpadding="0" cellspacing="0" align="center"
                	bgcolor="#FFFFFF"
                	width="560" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
                	max-width: 560px;" class="container">
                	<tr>
                		<td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%; font-size: 24px; font-weight: bold; line-height: 130%;
                			padding-top: 25px;
                			color: #000000;
                			font-family: sans-serif;" class="header">
                			JagCab Invoice
                		</td>
                		<td>
                		    <img border="0" vspace="0" hspace="0"
                				src="https://jag.cab/assets/website/assets/img/logo.png"
                				width="100" height="30"
                				alt="Logo" title="Logo" style="
                				color: #000000;
                				font-size: 10px; margin: 0; padding: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: block;" />
                		</td>
                	</tr>
                	<tr>	
                		<td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%;
                			padding-top: 25px;" class="line"><hr
                			color="#E0E0E0" align="center" width="100%" size="1" noshade style="margin: 0; padding: 0;" />
                		</td>
                	</tr>
                	<tr>
                		<td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%;" class="list-item"><table align="center" border="0" cellspacing="0" cellpadding="0" style="width: inherit; margin: 0; padding: 0; border-collapse: collapse; border-spacing: 0;">
                			<tr>
                				<td align="left" valign="top" style="font-size: 17px; font-weight: 400; line-height: 160%; border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;
                					padding-top: 25px;
                					color: #000000;
                					font-family: sans-serif;" class="paragraph">
                						<b style="color: #333333;">GST No:</b> 03AAECJ9193Q1ZU<br/>
                						<b style="color: #333333;">Customer Receipt - Transaction ID #: </b>CHD '.$request_detail->request_number.' C<br/>
                				</td>
                			</tr>
                            <tr>
                				<td align="left" valign="top" style="font-size: 14px; font-weight: 400;line-height: 160%; border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;
                					padding-top: 5px;
                					color: #000000;
                					font-family: sans-serif;" class="paragraph">
                						<b>Date:</b> '.date('Y-m-d',strtotime($request_detail->trip_start_time)).'
                				</td>
                			</tr>
                			<tr>
                				<td align="left" valign="top" style="font-size: 14px; font-weight: 400; line-height: 160%; border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;
                					padding-top: 0;
                					color: #000000;
                					font-family: sans-serif;" class="paragraph">
                					    <table style="width:382px; margin-top:0px; margin-bottom:0px;">
                						    <tr>
                						    <td><b>Start Time:</b> '.date('H:i A',strtotime($request_detail->trip_start_time)).'<br></td>
                						    <td><b>Finish Time:</b> '.date('H:i A',strtotime($request_detail->completed_at)).'<br></td>
                						</tr>
                						</table>
                						<table style="width:500px; margin-top:0px; margin-bottom:0px;">
                						    <tr>
                						    <td><b>Origin:</b> '.$request_places->pick_address.'<br></td>
                						    
                						</tr>
                						 <tr>
                						    <td><b>Destination:</b> '.$request_places->drop_address.'<br></td>
                						</tr>
                						</table>
                						<table style="width:500px; margin-top:0px; margin-bottom:0px;">
                						    <tr>
                						        <td><b>Route Distance (Kms):</b> '.$request_detail->total_distance.'<br></td>
                    						</tr>
                    						 <tr>
                    						    <td><b>Product:</b> Rs '.$request_detail->request_eta_amount.' Flat Rate TriCity Ride<br></td>
                    						</tr>
                						</table>
                					
                					     <table style="width:500px; margin-top:0px; margin-bottom:0px;">
                						    <tr>
                						        <td><b>Customer Name:</b> '.$user->name.'<br></td>
                    						</tr>
                    						 <tr>
                    						    <td><b>Customer Email Address:</b> '.$user->email.'<br></td>
                    						</tr>
                    						<tr>
                    						    <td><b>Mobile No:</b> '.$user->mobile.'<br></td>
                    						</tr>
                						</table>
                						
                						<table style="width:500px; margin-top:10px; margin-bottom:10px;">
                						    <tr>
                						    <td><b>Billed Amount:</b> '.$request_detail->request_eta_amount.'<br></td>
                						    <td><b>Payment Mode:</b> Cash<br></td>
                						</tr>
                						</table>
                						<table style="width:500px; margin-top:10px; margin-bottom:10px;">
                						    <tr>
                						        <td><b>GST@5%:</b> (Rs): '.number_format($request_detail->request_eta_amount - ($request_detail->request_eta_amount/1.05),2).'<br></td>
                						    </tr>
                						</table>
                						
                						<table style="width:500px; margin-top:10px; margin-bottom:20px;">
                						    <tr>
                						        <td>Available Credit Points: 0.00 New Added: 0.00 Last Redeemed: 0.00</td>
                        					</tr>
                						    <tr>
                						        <td>Available Referral Cash: 0.00 New Added: 0.00Last Redeemed 0.00 </td>
                					    	</tr>
                					    </table>
                				</td>
                			</tr>
                		</table></td>
                	</tr>
                </table>
                		</td>
                	</tr>
                </table>
                </td></tr></table>
                </body>
                </html>';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: <info@jagcab.com>' . "\r\n";
            mail($user->email,$subject,$message,$headers);
        
        
        // if($driver->driver_type==2){
            
        //     $subject = "Trip Invoice";
        //     $message = '<html xmlns="http://www.w3.org/1999/xhtml"><head>
        //         	<meta http-equiv="content-type" content="text/html; charset=utf-8">
        //           	<meta name="viewport" content="width=device-width, initial-scale=1.0;">
        //          	<meta name="format-detection" content="telephone=no"/>
        //         	<style>
        //         body { margin: 0; padding: 0; min-width: 100%; width: 100% !important; height: 100% !important;}
        //         body, table, td, div, p, a { -webkit-font-smoothing: antialiased; text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; line-height: 100%; }
        //         table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse !important; border-spacing: 0; }
        //         img { border: 0; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
        //         #outlook a { padding: 0; }
        //         .ReadMsgBody { width: 100%; } .ExternalClass { width: 100%; }
        //         .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
        //         @media all and (min-width: 560px) {
        //         	.container { border-radius: 8px; -webkit-border-radius: 8px; -moz-border-radius: 8px; -khtml-border-radius: 8px;}
        //         }
        //         a, a:hover {
        //         	color: #127DB3;
        //         }
        //         .footer a, .footer a:hover {
        //         	color: #999999;
        //         }
        //          	</style>
        //         	<title>Invoice Template</title>
        //         </head>
        //         <body topmargin="0" rightmargin="0" bottommargin="0" leftmargin="0" marginwidth="0" marginheight="0" width="100%" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 100%; height: 100%; -webkit-font-smoothing: antialiased; text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; line-height: 100%;
        //         	background-color: #F0F0F0;
        //         	color: #000000;"
        //         	bgcolor="#F0F0F0"
        //         	text="#000000">
        //         <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 100%;" class="background"><tr><td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;"
        //         	bgcolor="#F0F0F0">
        //         <table border="0" cellpadding="0" cellspacing="0" align="center"
        //         	bgcolor="#FFFFFF"
        //         	width="560" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
        //         	max-width: 560px;" class="container">
        //         	<tr>
        //         		<td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%; font-size: 24px; font-weight: bold; line-height: 130%;
        //         			padding-top: 25px;
        //         			color: #000000;
        //         			font-family: sans-serif;" class="header">
        //         			JagCab Invoice
        //         		</td>
        //         		<td>
        //         		    <img border="0" vspace="0" hspace="0"
        //         				src="https://jag.cab/assets/website/assets/img/logo.png"
        //         				width="100" height="30"
        //         				alt="Logo" title="Logo" style="
        //         				color: #000000;
        //         				font-size: 10px; margin: 0; padding: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: block;" />
        //         		</td>
        //         	</tr>
        //         	<tr>	
        //         		<td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%;
        //         			padding-top: 25px;" class="line"><hr
        //         			color="#E0E0E0" align="center" width="100%" size="1" noshade style="margin: 0; padding: 0;" />
        //         		</td>
        //         	</tr>
        //         	<tr>
        //         		<td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%;" class="list-item"><table align="center" border="0" cellspacing="0" cellpadding="0" style="width: inherit; margin: 0; padding: 0; border-collapse: collapse; border-spacing: 0;">
        //         			<tr>
        //         				<td align="left" valign="top" style="font-size: 17px; font-weight: 400; line-height: 160%; border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;
        //         					padding-top: 25px;
        //         					color: #000000;
        //         					font-family: sans-serif;" class="paragraph">
        //         						<b style="color: #333333;">Driver Receipt - Transaction ID #: '.$request_detail->request_number.' </b><br/>
        //         				</td>
        //         			</tr>
        //                     <tr>
        //         				<td align="left" valign="top" style="font-size: 17px; font-weight: 400; line-height: 160%; border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;
        //         					padding-top: 5px;
        //         					color: #000000;
        //         					font-family: sans-serif;" class="paragraph">
        //         						Start:'.$request_detail->trip_start_time.'
        //         				</td>
        //         			</tr>
        //         			<tr>
        //         				<td align="left" valign="top" style="font-size: 17px; font-weight: 400; line-height: 160%; border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;
        //         					padding-top: 0;
        //         					color: #000000;
        //         					font-family: sans-serif;" class="paragraph">
        //         					    <table style="width:382px; margin-top:0px; margin-bottom:0px;">
        //         						    <tr>
        //         						    <td>Start Time: '.$request_detail->trip_start_time.'<br></td>
        //         						    <td>Finish Time:'.$request_detail->completed_at.'<br></td>
        //         						</tr>
        //         						</table>
        //         						<table style="width:500px; margin-top:0px; margin-bottom:0px;">
        //         						    <tr>
        //         						    <td>Origin:'.$request_places->pick_address.'<br></td>
        //         						    <td>Destination:'.$request_places->drop_address.'<br></td>
        //         						</tr>
        //         						</table>
        //         						Total Distance:'.$request_detail->total_distance.'<br>
        //         						Product: Flat Rate Ride<br>
        //         						<table style="width:500px; margin-top:10px; margin-bottom:10px;">
        //         						    <tr>
        //         						    <td>Billed Amount:'.$request_detail->request_eta_amount.'<br></td>
        //         						    <td>Payment Mode:Cash<br></td>
        //         						</tr>
        //         						</table>
        //         						<table style="width:500px; margin-top:20px; margin-bottom:20px;">
        //         						    <tr>
        //         						    <td><b>Financial Split </b></td>
        //                 						</tr>
        //         						    <tr>
        //         						    <td>Driver’s Earnings:'.$request_detail->request_eta_amount.' <br></td>
        //         						</tr>
        //         						</table>
        //         				</td>
        //         			</tr>
        //         		</table></td>
        //         	</tr>
        //         </table>
        //         		</td>
        //         	</tr>
        //         </table>
        //         </td></tr></table>
        //         </body>
        //         </html>';
        //     // Always set content-type when sending HTML email
        //     $headers = "MIME-Version: 1.0" . "\r\n";
        //     $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        //     // More headers
        //     $headers .= 'From: <info@jagcab.com>' . "\r\n";
        //     mail($driver->email,$subject,$message,$headers);
        // }else {
        
            //send email to customer
            $subject = "Trip Invoice";
            $message = '<html xmlns="http://www.w3.org/1999/xhtml"><head>
                	<meta http-equiv="content-type" content="text/html; charset=utf-8">
                  	<meta name="viewport" content="width=device-width, initial-scale=1.0;">
                 	<meta name="format-detection" content="telephone=no"/>
                	<style>
                body { margin: 0; padding: 0; min-width: 100%; width: 100% !important; height: 100% !important;}
                body, table, td, div, p, a { -webkit-font-smoothing: antialiased; text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; line-height: 100%; }
                table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse !important; border-spacing: 0; }
                img { border: 0; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
                #outlook a { padding: 0; }
                .ReadMsgBody { width: 100%; } .ExternalClass { width: 100%; }
                .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
                @media all and (min-width: 560px) {
                	.container { border-radius: 8px; -webkit-border-radius: 8px; -moz-border-radius: 8px; -khtml-border-radius: 8px;}
                }
                a, a:hover {
                	color: #127DB3;
                }
                .footer a, .footer a:hover {
                	color: #999999;
                }
                 	</style>
                	<title>Invoice Template</title>
                </head>
                <body topmargin="0" rightmargin="0" bottommargin="0" leftmargin="0" marginwidth="0" marginheight="0" width="100%" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 100%; height: 100%; -webkit-font-smoothing: antialiased; text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; line-height: 100%;
                	background-color: #F0F0F0;
                	color: #000000;"
                	bgcolor="#F0F0F0"
                	text="#000000">
                <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 100%;" class="background"><tr><td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;"
                	bgcolor="#F0F0F0">
                <table border="0" cellpadding="0" cellspacing="0" align="center"
                	bgcolor="#FFFFFF"
                	width="560" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
                	max-width: 560px;" class="container">
                	<tr>
                		<td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%; font-size: 24px; font-weight: bold; line-height: 130%;padding-top: 25px;color: #000000;font-family: sans-serif;" class="header">
                			JagCab Invoice
                		</td>
                		<td>
                		    <img border="0" vspace="0" hspace="0" src="https://jag.cab/assets/website/assets/img/logo.png" width="100" height="30" alt="Logo" title="Logo" style="color: #000000;font-size: 10px; margin: 0; padding: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: block;" />
                		</td>
                	</tr>
                	<tr>	
                		<td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%;
                			padding-top: 25px;" class="line"><hr
                			color="#E0E0E0" align="center" width="100%" size="1" noshade style="margin: 0; padding: 0;" />
                		</td>
                	</tr>
                	<tr>
                		<td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%;" class="list-item"><table align="center" border="0" cellspacing="0" cellpadding="0" style="width: inherit; margin: 0; padding: 0; border-collapse: collapse; border-spacing: 0;">
                			<tr>
                				<td align="left" valign="top" style="font-size: 17px; font-weight: 400; line-height: 160%; border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;padding-top: 25px;color: #000000;font-family: sans-serif;" class="paragraph">
                						<b style="color: #333333;">Total Amount Earned in the Day: Rs</b> '.$request_detail->request_eta_amount.'<br/>
                						<b style="color: #333333;">GST No:</b> 03AAECJ9193Q1ZU<br/>
                						<b style="color: #333333;">Driver('.$driver->name.') Intimation #: CHD '.$request_detail->request_number.' C </b><br/>
                				</td>
                			</tr>
                            <tr>
                				<td align="left" valign="top" style="font-size: 14px; font-weight: 400; line-height: 160%; border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;padding-top: 5px;color: #000000;font-family: sans-serif;" class="paragraph">
                				   <b>Start:</b> '.date('Y-m-d',strtotime($request_detail->trip_start_time)).'
                				</td>
                			</tr>
                			<tr>
                				<td align="left" valign="top" style="font-size: 14px; font-weight: 400; line-height: 160%; border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;padding-top: 0;color: #000000;font-family: sans-serif;" class="paragraph">
                					    <table style="width:382px; margin-top:0px; margin-bottom:0px;">
                						    <tr>
                						    <td><b>Start Time:</b> '.date('H:i A',strtotime($request_detail->trip_start_time)).'<br></td>
                						    <td><b>Finish Time:</b> '.date('H:i A',strtotime($request_detail->completed_at)).'<br></td>
                						</tr>
                						</table>
                						<table style="width:500px; margin-top:0px; margin-bottom:0px;">
                						    <tr>
                						    <td><b>Origin:</b> '.$request_places->pick_address.'<br></td>
                						</tr>
                						<tr>
                						    <td><b>Destination:</b> '.$request_places->drop_address.'<br></td>
                						</tr>
                						</table>
                						<table style="width:500px; margin-top:0px; margin-bottom:0px;">
                						    <tr>
                						    <td><b>Route Distance (Kms)::</b> '.$request_detail->total_distance.'<br></td>
                						</tr>
                						<tr>
                						    <td><b>Product:</b> Rs '.$request_detail->request_eta_amount.' Flat Rate Tricity Ride<br></td>
                						</tr>
                						</table>
                						<table style="width:500px; margin-top:10px; margin-bottom:10px;">
                						    <tr>
                						    <td><b>Billed Amount:</b> '.$request_detail->request_eta_amount.'<br></td>
                						    <td><b>Payment Mode:</b> Cash<br></td>
                						</tr>
                						</table>
                						<table style="width:500px; margin-top:10px; margin-bottom:10px;">
                						    <tr>
                						        <td><b>GST@5%:</b> (Rs): '.number_format($request_detail->request_eta_amount - ($request_detail->request_eta_amount/1.05),2).'<br></td>
                						    </tr>
                						</table>
                						
                						<table style="width:500px; margin-top:10px; margin-bottom:20px;">
                						    <tr>
                						        <td>Driver Commission: '.$drivercommision.'</td>
                        					</tr>
                						</table>
                				</td>
                			</tr>
                		</table></td>
                	</tr>
                </table>
                		</td>
                	</tr>
                </table>
                </td></tr></table>
                </body>
                </html>';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: <info@jagcab.com>' . "\r\n";
            mail($driver->email,$subject,$message,$headers);
        //}
        
        if ($request_detail->is_completed) {
            // @TODO send success response with bill object
            // $this->throwCustomException('request completed already');
            $request_result = fractal($request_detail, new TripRequestTransformer)->parseIncludes('requestBill');
            return $this->respondSuccess($request_result, 'request_ended');
        }
        if ($request_detail->is_cancelled) {
            $this->throwCustomException('request cancelled');
        }

        $firebase_request_detail = $this->database->getReference('requests/'.$request_detail->id)->getValue();

        $request_place_params = ['drop_lat'=>$request->drop_lat,'drop_lng'=>$request->drop_lng,'drop_address'=>$request->drop_address];

        if ($firebase_request_detail) {
            if(array_key_exists('lat_lng_array',$firebase_request_detail)){
                $locations = $firebase_request_detail['lat_lng_array'];
                $request_place_params['request_path'] = $locations;
            }
        }
        // Remove firebase data
        // $this->database->getReference('requests/'.$request_detail->id)->remove();

        // Update Droped place details
        $request_detail->requestPlace->update($request_place_params);
        // Update Driver state as Available
        $request_detail->driverDetail->update(['available'=>true]);
        // Get currency code of Request
        $service_location = $request_detail->zoneType->zone->serviceLocation;
        $currency_code = $service_location->currency_code;
        
        // $currency_code = get_settings('currency_code');
        $requested_currency_symbol = $service_location->currency_symbol;

        


        $distance_matrix = get_distance_matrix($request_detail->pick_lat, $request_detail->pick_lng, $request_detail->drop_lat, $request_detail->drop_lng, true);

        $distance = (double)$request->distance;
        $duration = $this->calculateDurationOfTrip($request_detail->trip_start_time);

        // if ($distance_matrix->status =="OK" && $distance_matrix->rows[0]->elements[0]->status != "ZERO_RESULTS") {
        //     $distance_in_meters = get_distance_value_from_distance_matrix($distance_matrix);
        //     $distance = $distance_in_meters / 1000;

        //     if ($distance < $request->distance) {
        //         $distance = (double)$request->distance;
        //     }

        //     //If we need we can use these lines
        //     // $duration = get_duration_text_from_distance_matrix($distance_matrix);
        //     // $duration_in_mins = explode(' ', $duration);
        //     // $duration = (double)$duration_in_mins[0];
        // }
        if ($request_detail->unit==UnitType::MILES) {
            $distance = kilometer_to_miles($distance);
        }

        // Update Request status as completed
        $request_detail->update([
            'is_completed'=>true,
            'completed_at'=>date('Y-m-d H:i:s'),
            'is_paid'=>1,
            //'total_distance'=>$distance,
            //'total_time'=>$duration,
            ]);

        $before_trip_start_waiting_time = $request->input('before_trip_start_waiting_time');
        $after_trip_start_waiting_time = $request->input('after_trip_start_waiting_time');

        $subtract_with_free_waiting_before_trip_start = ($before_trip_start_waiting_time - $zone_type_price->free_waiting_time_in_mins_before_trip_start);

        $subtract_with_free_waiting_after_trip_start = ($after_trip_start_waiting_time - $zone_type_price->free_waiting_time_in_mins_after_trip_start);

        $waiting_time = ($subtract_with_free_waiting_before_trip_start+$subtract_with_free_waiting_after_trip_start);

        if($waiting_time<0){
            $waiting_time = 0;
        }

        // Calculated Fares
        $promo_detail =null;

        if ($request_detail->promo_id) {
            $promo_detail = $this->validateAndGetPromoDetail($request_detail->promo_id);
        }

        $calculated_bill =  $this->calculateRideFares($zone_type_price, $distance, $duration, $waiting_time, $promo_detail,$request_detail);

        $calculated_bill['before_trip_start_waiting_time'] = $before_trip_start_waiting_time;
        $calculated_bill['after_trip_start_waiting_time'] = $after_trip_start_waiting_time;
        $calculated_bill['calculated_waiting_time'] = $waiting_time;
        $calculated_bill['waiting_charge_per_min'] = $zone_type_price->waiting_charge;

        if($request_detail->is_rental && $request_detail->rental_package_id){

            $chosen_package_price = ZoneTypePackagePrice::where('zone_type_id',$request_detail->zone_type_id)->where('package_type_id',$request_detail->rental_package_id)->first();

            $previous_range = 0;
            $exceeding_range = 0;
            $package= null;            

        $zone_type_package_prices = $zone_type->zoneTypePackage()->orderBy('free_min','asc')->get();


        foreach ($zone_type_package_prices as $key => $zone_type_package_price) {            
            
            if($zone_type_package_price->free_min == $duration){
                $package = $zone_type_package_price;
                
                break;
            }
            elseif($zone_type_package_price->free_min < $duration){
                $previous_range = $zone_type_package_price->free_min;
                $previous_zone_type = $zone_type_package_price;
            }
            else{
                $exceeding_range = $zone_type_package_price->free_min;
                $exceeding_zone_type = $zone_type_package_price;
            }

            if($exceeding_range != 0 && $package == null){
                $package = ($previous_range == 0) ? $exceeding_zone_type : $previous_zone_type;
               

                break;

            } else {
                $package = $previous_zone_type;

               
            }
        }

        if($package){

            $zone_type_price = $package;
        }else{

            $zone_type_price = $chosen_package_price;
        }

        $request_detail->rental_package_id = $zone_type_price->package_type_id;
        $request_detail->save();

          $calculated_bill =  $this->calculateRentalRideFares($zone_type_price, $distance, $duration, $waiting_time, $promo_detail,$request_detail);

          // Log::info($calculated_bill);
            
        }


        $calculated_bill['requested_currency_code'] = $currency_code;
        $calculated_bill['requested_currency_symbol'] = $requested_currency_symbol;
        // @TODO need to take admin commision from driver wallet
        if ($request_detail->payment_opt==PaymentType::CASH) {

            // Deduct the admin commission + tax from driver walllet
            $admin_commision_with_tax = $calculated_bill['admin_commision_with_tax'];
            if($request_detail->driverDetail->owner()->exists()){

            $owner_wallet = $request_detail->driverDetail->owner->ownerWalletDetail;
            $owner_wallet->amount_spent += $admin_commision_with_tax;
            $owner_wallet->amount_balance -= $admin_commision_with_tax;
            $owner_wallet->save();

            $owner_wallet_history = $request_detail->driverDetail->owner->ownerWalletHistoryDetail()->create([
                'amount'=>$admin_commision_with_tax,
                'transaction_id'=>str_random(6),
                'remarks'=>WalletRemarks::ADMIN_COMMISSION_FOR_REQUEST,
                'is_credit'=>false
            ]);


            }else{

                $driver_wallet = $request_detail->driverDetail->driverWallet;
            $driver_wallet->amount_spent += $admin_commision_with_tax;
            $driver_wallet->amount_balance -= $admin_commision_with_tax;
            $driver_wallet->save();

            $driver_wallet_history = $request_detail->driverDetail->driverWalletHistory()->create([
                'amount'=>$admin_commision_with_tax,
                'transaction_id'=>str_random(6),
                'remarks'=>WalletRemarks::ADMIN_COMMISSION_FOR_REQUEST,
                'is_credit'=>false
            ]);

            }
            

        } elseif ($request_detail->payment_opt==PaymentType::CARD) {
            // @TODO in future
        } else { //PaymentType::WALLET
            // To Detect Amount From User's Wallet
            // Need to check if the user has enough amount to spent for his trip
            $chargable_amount = $calculated_bill['total_amount'];
            $user_wallet = $request_detail->userDetail->userWallet;

            if ($chargable_amount<=$user_wallet->amount_balance) {
                $user_wallet->amount_balance -= $chargable_amount;
                $user_wallet->amount_spent += $chargable_amount;
                $user_wallet->save();

                $user_wallet_history = $request_detail->userDetail->userWalletHistory()->create([
                'amount'=>$chargable_amount,
                'transaction_id'=>$request_detail->id,
                'request_id'=>$request_detail->id,
                'remarks'=>WalletRemarks::SPENT_FOR_TRIP_REQUEST,
                'is_credit'=>false]);

                // @TESTED to add driver commision if the payment type is wallet
                if($request_detail->driverDetail->owner()->exists()){

                $driver_commision = $calculated_bill['driver_commision'];
                $owner_wallet = $request_detail->driverDetail->owner->ownerWalletDetail;
                $owner_wallet->amount_added += $driver_commision;
                $owner_wallet->amount_balance += $driver_commision;
                $owner_wallet->save();

                $owner_wallet_history = $request_detail->driverDetail->owner->ownerWalletHistoryDetail()->create([
                'amount'=>$driver_commision,
                'transaction_id'=>$request_detail->id,
                'remarks'=>WalletRemarks::TRIP_COMMISSION_FOR_DRIVER,
                'is_credit'=>true
            ]);


                }else{

                $driver_commision = $calculated_bill['driver_commision'];
                $driver_wallet = $request_detail->driverDetail->driverWallet;
                $driver_wallet->amount_added += $driver_commision;
                $driver_wallet->amount_balance += $driver_commision;
                $driver_wallet->save();

                $driver_wallet_history = $request_detail->driverDetail->driverWalletHistory()->create([
                'amount'=>$driver_commision,
                'transaction_id'=>$request_detail->id,
                'remarks'=>WalletRemarks::TRIP_COMMISSION_FOR_DRIVER,
                'is_credit'=>true
            ]);
                }

            } else {
                $request_detail->payment_opt = PaymentType::CASH;
                $request_detail->save();
                $admin_commision_with_tax = $calculated_bill['admin_commision_with_tax'];

                if($request_detail->driverDetail->owner()->exists()){
                     $owner_wallet = $request_detail->driverDetail->owner->ownerWalletDetail;
            $owner_wallet->amount_spent += $admin_commision_with_tax;
            $owner_wallet->amount_balance -= $admin_commision_with_tax;
            $owner_wallet->save();

            $owner_wallet_history = $request_detail->driverDetail->owner->ownerWalletHistoryDetail()->create([
                'amount'=>$admin_commision_with_tax,
                'transaction_id'=>str_random(6),
                'remarks'=>WalletRemarks::ADMIN_COMMISSION_FOR_REQUEST,
                'is_credit'=>false
            ]);
        }else{
                $driver_wallet = $request_detail->driverDetail->driverWallet;
                $driver_wallet->amount_spent += $admin_commision_with_tax;
                $driver_wallet->amount_balance -= $admin_commision_with_tax;
                $driver_wallet->save();

                $driver_wallet_history = $request_detail->driverDetail->driverWalletHistory()->create([
                'amount'=>$admin_commision_with_tax,
                'transaction_id'=>str_random(6),
                'remarks'=>WalletRemarks::ADMIN_COMMISSION_FOR_REQUEST,
                'is_credit'=>false
            ]);
        }
               
            }
        }
        // @TODO need to add driver commision if the payment type is wallet
        // Store Request bill

        $bill = $request_detail->requestBill()->create($calculated_bill);
    
        // Log::info($bill);

        $request_result = fractal($request_detail, new TripRequestTransformer)->parseIncludes(['requestBill','userDetail','driverDetail']);
        //echo "<pre>"; print_r($request_result); die();
        if ($request_detail->if_dispatch || $request_detail->user_id==null ) {
            goto dispatch_notify;
        }
        // Send Push notification to the user
        $user = $request_detail->userDetail;
        $title = trans('push_notifications.trip_completed_title',[],$user->lang);
        $body = trans('push_notifications.trip_completed_body',[],$user->lang);


       
            
            
            
        $pus_request_detail = $request_result->toJson();
        $push_data = ['notification_enum'=>PushEnums::DRIVER_END_THE_TRIP,'result'=>(string)$pus_request_detail];

        $socket_data = new \stdClass();
        $socket_data->success = true;
        $socket_data->success_message  = PushEnums::DRIVER_END_THE_TRIP;
        $socket_data->result = $request_result;
        // Form a socket sturcture using users'id and message with event name
        // $socket_message = structure_for_socket($user->id, 'user', $socket_data, 'trip_status');
        // dispatch(new NotifyViaSocket('transfer_msg', $socket_message));

        // dispatch(new NotifyViaMqtt('trip_status_'.$user->id, json_encode($socket_data), $user->id));

        $user->notify(new AndroidPushNotification($title, $body));
        dispatch_notify:
        // @TODO Send email & sms
        return $this->respondSuccess($request_result, 'request_ended');
    }

    public function calculateDurationOfTrip($start_time)
    {
        $current_time = date('Y-m-d H:i:s');
        $start_time = Carbon::parse($start_time);
        $end_time = Carbon::parse($current_time);
        $totald_duration = $end_time->diffInMinutes($start_time);

        return $totald_duration;
    }

    /**
    * Calculate Ride fares
    *
    */
    public function calculateRideFares($zone_type_price, $distance, $duration, $waiting_time, $coupon_detail,$request_detail)
    {   
        $request_place = $request_detail->requestPlace;

        $airport_surge = find_airport($request_place->pick_lat,$request_place->pick_lng);
        if($airport_surge==null){
            $airport_surge = find_airport($request_place->drop_lat,$request_place->drop_lng);
        }

        $airport_surge_fee = 0;

        if($airport_surge){

            $airport_surge_fee = $airport_surge->airport_surge_fee?:0;

        }


        // Distance Price
        $calculatable_distance = $distance - $zone_type_price->base_distance;
        $calculatable_distance = $calculatable_distance<0?0:$calculatable_distance;

        $price_per_distance = $zone_type_price->price_per_distance;

        // Validate if the current time in surge timings

        $timezone = $request_detail->serviceLocationDetail->timezone;

        $current_time = Carbon::now()->setTimezone($timezone);

        $current_time = $current_time->toTimeString();

        $zone_surge_price = $request_detail->zoneType->zone->zoneSurge()->whereTime('start_time','<=',$current_time)->whereTime('end_time','>=',$current_time)->first();

        if($zone_surge_price){

            $surge_percent = $zone_surge_price->value;

            $surge_price_additional_cost = ($price_per_distance * ($surge_percent / 100));

            $price_per_distance += $surge_price_additional_cost;

            $request_detail->is_surge_applied = true;

            $request_detail->save();

        }

        $distance_price = round(($calculatable_distance * $price_per_distance),0);

        // Time Price
        $time_price = round($duration * $zone_type_price->price_per_time,0);
        // Waiting charge
        $waiting_charge = round($waiting_time * $zone_type_price->waiting_charge,0);
        // Base Price
        $base_price = round($zone_type_price->base_price,0);

        // Sub Total

        if($request_detail->zoneType->vehicleType->is_support_multiple_seat_price && $request_detail->passenger_count > 0){

            if($request_detail->passenger_count ==1){
                $seat_discount = $request_detail->zoneType->vehicleType->one_seat_price_discount;
            }
            if($request_detail->passenger_count ==2){
                $seat_discount = $request_detail->zoneType->vehicleType->two_seat_price_discount;
            }
            if($request_detail->passenger_count ==3){
                $seat_discount = $request_detail->zoneType->vehicleType->three_seat_price_discount;
            }
            if($request_detail->passenger_count ==4){
                $seat_discount = $request_detail->zoneType->vehicleType->four_seat_price_discount;
            }

            // $price_discount = ($sub_total * ($seat_discount / 100));


            // $sub_total -= $price_discount; 

            $base_price -= ($base_price * ($seat_discount / 100));

            $distance_price -=  ($distance_price * ($seat_discount / 100));

            $time_price -=  ($time_price * ($seat_discount / 100));

            $airport_surge_fee -= ($airport_surge_fee * ($seat_discount / 100));

        }

        $sub_total = round(($base_price+$distance_price+$time_price+$waiting_charge + $airport_surge_fee),0);


        // Check for Cancellation fee

        $cancellation_fee = RequestCancellationFee::where('user_id',$request_detail->user_id)->where('is_paid',0)->sum('cancellation_fee');

        if($cancellation_fee >0){

            RequestCancellationFee::where('user_id',$request_detail->user_id)->update([
                'is_paid'=>1,
                'paid_request_id'=>$request_detail->id]);

            $sub_total += $cancellation_fee;

        }

        $discount_amount = 0;
        if ($coupon_detail) {
            if ($coupon_detail->minimum_trip_amount < $sub_total) {
                $discount_amount = $sub_total * ($coupon_detail->discount_percent/100);
                if ($discount_amount > $coupon_detail->maximum_discount_amount) {
                    $discount_amount = $coupon_detail->maximum_discount_amount;
                }
                $sub_total = $sub_total - $discount_amount;
            }
        }

        $sub_total = round($sub_total,0);

        // Get service tax percentage from settings
        $tax_percent = get_settings('service_tax');
        $tax_amount = round(($sub_total * ($tax_percent / 100)),0);
        // Get Admin Commision
        $admin_commision_type = get_settings('admin_commission_type');

        $service_fee = get_settings('admin_commission');
        // Admin commision
        if($admin_commision_type==1){

        $admin_commision = round(($sub_total * ($service_fee / 100)),0);

        }else{
            
            $admin_commision = round($service_fee,0);

        }
        // Admin commision with tax amount
        $admin_commision_with_tax = round(($tax_amount + $admin_commision),0);
        $driver_commision = round(($sub_total+$discount_amount),0);  
        // Driver Commission
        if($coupon_detail && $coupon_detail->deduct_from==2){
            $driver_commision = $sub_total;  
        }
        // Total Amount
        $total_amount = round($sub_total + $admin_commision_with_tax,0);
        if($distance<$zone_type_price->base_distance){
            $total_amount = 200;
            $tax_amount = 9.5;
            $base_price = 190.5;
        }
        return $result = [
        'base_price'=>$base_price,
        'base_distance'=>$zone_type_price->base_distance,
        'price_per_distance'=>$zone_type_price->price_per_distance,
        'distance_price'=>$distance_price,
        'price_per_time'=>$zone_type_price->price_per_time,
        'time_price'=>$time_price,
        'promo_discount'=>$discount_amount,
        'waiting_charge'=>$waiting_charge,
        'service_tax'=>$tax_amount,
        'service_tax_percentage'=>$tax_percent,
        'admin_commision'=>$admin_commision,
        'admin_commision_with_tax'=>$admin_commision_with_tax,
        'driver_commision'=>$driver_commision,
        'total_amount'=>$total_amount,
        'total_distance'=>$distance,
        'total_time'=>$duration,
        'airport_surge_fee'=>$airport_surge_fee,
        'cancellation_fee'=>$cancellation_fee
        ];
    }

       /**
    * Calculate Ride fares
    *
    */
    public function calculateRentalRideFares($zone_type_price, $distance, $duration, $waiting_time, $coupon_detail,$request_detail)
    {   
        $request_place = $request_detail->requestPlace;

        $airport_surge = find_airport($request_place->pick_lat,$request_place->pick_lng);
        if($airport_surge==null){
            $airport_surge = find_airport($request_place->drop_lat,$request_place->drop_lng);
        }

        $airport_surge_fee = 0;

        if($airport_surge){

            $airport_surge_fee = $airport_surge->airport_surge_fee?:0;

        }

        $airport_surge_fee = round($airport_surge_fee,0);

        // Distance Price
        $calculatable_distance = $distance - $zone_type_price->free_distance;
        $calculatable_distance = $calculatable_distance<0?0:$calculatable_distance;

        $price_per_distance = $zone_type_price->distance_price_per_km; 

        // Validate if the current time in surge timings

        $timezone = $request_detail->serviceLocationDetail->timezone;

        $current_time = Carbon::now()->setTimezone($timezone);

        $current_time = $current_time->toTimeString();

        $zone_surge_price = $request_detail->zoneType->zone->zoneSurge()->whereTime('start_time','<=',$current_time)->whereTime('end_time','>=',$current_time)->first();

        if($zone_surge_price){

            $surge_percent = $zone_surge_price->value;

            $surge_price_additional_cost = ($price_per_distance * ($surge_percent / 100));

            $price_per_distance += $surge_price_additional_cost;

            $request_detail->is_surge_applied = true;

            $request_detail->save();

        }

        $distance_price = round(($calculatable_distance * $price_per_distance),0);
        // Time Price
        $time_price = round(($duration * $zone_type_price->time_price_per_min),0);
        // Waiting charge
        $waiting_charge = round(($waiting_time * $zone_type_price->waiting_charge),0);
        // Base Price
        $base_price = round($zone_type_price->base_price,0);

        // Sub Total

        if($request_detail->zoneType->vehicleType->is_support_multiple_seat_price && $request_detail->passenger_count > 0){

            if($request_detail->passenger_count ==1){
                $seat_discount = $request_detail->zoneType->vehicleType->one_seat_price_discount;
            }
            if($request_detail->passenger_count ==2){
                $seat_discount = $request_detail->zoneType->vehicleType->two_seat_price_discount;
            }
            if($request_detail->passenger_count ==3){
                $seat_discount = $request_detail->zoneType->vehicleType->three_seat_price_discount;
            }
            if($request_detail->passenger_count ==4){
                $seat_discount = $request_detail->zoneType->vehicleType->four_seat_price_discount;
            }

            // $price_discount = ($sub_total * ($seat_discount / 100));


            // $sub_total -= $price_discount; 

            $base_price -= ($base_price * ($seat_discount / 100));

            $distance_price -=  ($distance_price * ($seat_discount / 100));

            $time_price -=  ($time_price * ($seat_discount / 100));

            $airport_surge_fee -= ($airport_surge_fee * ($seat_discount / 100));

        }

        $sub_total = round(($base_price+$distance_price+$time_price+$waiting_charge + $airport_surge_fee),0);


        $discount_amount = 0;

         if ($coupon_detail) {
            if ($coupon_detail->minimum_trip_amount < $sub_total) {

                $discount_amount = $sub_total * ($coupon_detail->discount_percent/100);
                if ($discount_amount > $coupon_detail->maximum_discount_amount) {
                    $discount_amount = $coupon_detail->maximum_discount_amount;
                }

                $discount_amount = round($discount_amount,0);

                $sub_total = $sub_total - $discount_amount;
            }
        }

        $sub_total = round($sub_total,0);

        // Get service tax percentage from settings
        $tax_percent = get_settings('service_tax');
        $tax_amount = round(($sub_total * ($tax_percent / 100)),0);
        // Get Admin Commision
        $service_fee = get_settings('admin_commission');
        // Admin commision
        // Admin commision
        // Get Admin Commision
        $admin_commision_type = get_settings('admin_commission_type');

        if($admin_commision_type==1){

        $admin_commision = round(($sub_total * ($service_fee / 100)),0);

        }else{
            
            $admin_commision = round($service_fee,0);

        }
        // Admin commision with tax amount
        $admin_commision_with_tax = round(($tax_amount + $admin_commision),0);
        $driver_commision = round(($sub_total+$discount_amount),0);  
        // Driver Commission
        if($coupon_detail && $coupon_detail->deduct_from==2){
            $driver_commision = round($sub_total,0);  
        }
        // Total Amount
        $total_amount = round(($sub_total + $admin_commision_with_tax),0);
        if($distance<$zone_type_price->base_distance){
            $total_amount = 200;
            $tax_amount = 9.5;
            $base_price = 190.5;
        }
        return $result = [
        'base_price'=>$base_price,
        'base_distance'=>$zone_type_price->free_distance,
        'price_per_distance'=>$zone_type_price->distance_price_per_km,
        'distance_price'=>$distance_price,
        'price_per_time'=>$zone_type_price->time_price_per_min,
        'time_price'=>$time_price,
        'promo_discount'=>$discount_amount,
        'waiting_charge'=>$waiting_charge,
        'service_tax'=>$tax_amount,
        'service_tax_percentage'=>$tax_percent,
        'admin_commision'=>$admin_commision,
        'admin_commision_with_tax'=>$admin_commision_with_tax,
        'driver_commision'=>$driver_commision,
        'total_amount'=>$total_amount,
        'total_distance'=>$distance,
        'total_time'=>$duration,
        'airport_surge_fee'=>$airport_surge_fee
        ];
    }

    /**
    * Validate & Apply Promo code
    *
    */
    public function validateAndGetPromoDetail($promo_code_id)
    {
       // Validate if the promo is expired
        $current_date = Carbon::today()->toDateTimeString();

        $expired = Promo::where('id', $promo_code_id)->where('to', '>', $current_date)->first();
        
        return $expired;
        
        // $exceed_usage = PromoUser::where('promo_code_id', $expired->id)->where('user_id', $user_id)->get()->count();

        // if ($exceed_usage >= $expired->uses_per_user) {
        //     return null;
        // }

        // if ($expired->total_uses > $expired->total_uses+1) {
        //     return null;
        // }
        
    }
}
