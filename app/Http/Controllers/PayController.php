<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\DonationReceipt;
use App\Models\Transaction;
use App\User;
use App\Models\Donation;
use App\Models\Organization;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PayController extends Controller
{
    private $donation;
    private $user;
    private $organization;
    private $transaction;

    public function __construct(Donation $donation, User $user, Organization $organization, Transaction $transaction)
    {
        $this->donation = $donation;
        $this->user = $user;
        $this->organization = $organization;
        $this->transaction = $transaction;
    }

    public function index(Request $request)
    {
        $feesid     = $request->id;
        $getfees    = DB::table('fees')->where('id', $feesid)->first();

        $getcat = DB::table('fees')
            ->join('fees_details', 'fees_details.fees_id', '=', 'fees.id')
            ->join('details', 'details.id', '=', 'fees_details.details_id')
            ->join('categories', 'categories.id', '=', 'details.category_id')
            ->distinct('categories.nama')
            ->select('categories.id as cid', 'categories.nama as cnama')
            ->where('fees.id', $feesid)
            ->orderBy('categories.id')
            ->get();

        $getdetail  = DB::table('fees')
            ->join('fees_details', 'fees_details.fees_id', '=', 'fees.id')
            ->join('details', 'details.id', '=', 'fees_details.details_id')
            ->join('categories', 'categories.id', '=', 'details.category_id')
            ->select('categories.id as cid', 'categories.nama as cnama', 'details.nama as dnama', 'details.quantity as quantity', 'details.price as price', 'details.totalamount as totalamount', 'details.id as did')
            ->where('fees.id', $feesid)
            ->orderBy('details.nama')
            ->get();
        return view('pentadbir.fee.pay', compact('getfees', 'getcat', 'getdetail'));
    }

    public function donateindex(Request $request)
    {
        $user = "";

        $donationId   = $request->id;
        $donation  = $this->donation->getDonationById($donationId);

        if (Auth::id()) {
            $user = $this->user->getUserById();
        }

        return view('paydonate.pay', compact('donation', 'user'));
    }

    // public function parentpay(Request $request)
    // {
    //     $feesid     = $request->id;
    //     $getfees    = DB::table('fees')->where('id', $feesid)->first();

    //     $getcat = DB::table('fees')
    //         ->join('fees_details', 'fees_details.fees_id', '=', 'fees.id')
    //         ->join('details', 'details.id', '=', 'fees_details.details_id')
    //         ->join('categories', 'categories.id', '=', 'details.category_id')
    //         ->distinct('categories.nama')
    //         ->select('categories.id as cid', 'categories.nama as cnama')
    //         ->where('fees.id', $feesid)
    //         ->orderBy('categories.id')
    //         ->get();

    //     $getdetail  = DB::table('fees')
    //         ->join('fees_details', 'fees_details.fees_id', '=', 'fees.id')
    //         ->join('details', 'details.id', '=', 'fees_details.details_id')
    //         ->join('categories', 'categories.id', '=', 'details.category_id')
    //         ->select('categories.id as cid', 'categories.nama as cnama', 'details.nama as dnama', 'details.quantity as quantity', 'details.price as price', 'details.totalamount as totalamount', 'details.id as did')
    //         ->where('fees.id', $feesid)
    //         ->orderBy('details.nama')
    //         ->get();
    //     return view('parent.fee.index', compact('getfees', 'getcat', 'getdetail'));
    // }

    public function fees_pay(Request $request)
    {
        $size = count(collect($request)->get('id'));
        $data = collect($request)->get('id');
        // dd($data[0]);

        $studentid  = array();
        $feesid     = array();
        $detailsid  = array();
        for ($i = 0; $i < $size; $i++) {

            //want seperate data from request
            //case 0 = student id
            //case 1 = fees id
            //case 3 = details id
            $case           = explode("-", $data[$i]);
            $studentid[]    = $case[0];
            $feesid[]       = $case[1];
            $detailsid[]    = $case[2];
        }
        $res_student = array_unique($studentid);
        $res_fee     = array_unique($feesid);
        $res_details = array_unique($detailsid);

        // $getstudent  = Student::whereIn('id', $res_student)->get();
        $getstudent  = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->join('class_fees', 'class_fees.class_organization_id', '=', 'class_organization.id')
            ->join('fees', 'class_fees.fees_id', '=', 'fees.id')
            ->join('organizations', 'organizations.id', '=', 'class_organization.organization_id')
            ->select('students.id as studentid', 'students.nama as studentname', 'fees.id as feeid', 'organizations.id as organizationid')
            ->whereIn('students.id', $res_student)
            ->get();

        $getstudent2  = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->join('class_fees', 'class_fees.class_organization_id', '=', 'class_organization.id')
            ->join('fees', 'class_fees.fees_id', '=', 'fees.id')
            ->join('organizations', 'organizations.id', '=', 'class_organization.organization_id')
            ->select('students.id as studentid', 'students.nama as studentname', 'fees.id as feeid', 'organizations.id as organizationid')
            ->whereIn('students.id', $res_student)
            ->first();

        $getorganization  = DB::table('organizations')
            ->where('id', $getstudent2->organizationid)
            ->first();

        // dd($getorganization);
        $getfees     = DB::table('fees')->whereIn('id', $res_fee)->get();

        $getdetails  = DB::table('details')
            ->join('fees_details', 'fees_details.details_id', '=', 'details.id')
            ->join('fees', 'fees.id', '=', 'fees_details.fees_id')
            ->select('details.id as detailsid', 'details.nama as dnama', 'details.quantity as quantity', 'details.price as price', 'fees.id as feeid')
            ->whereIn('details.id', $res_details)->get();
        // dd($getdetails);

        return view('parent.fee.pay', compact('getstudent', 'getfees', 'getdetails', 'getorganization'))->render();
    }

    public function billIndex()
    {
        $data['users'] = User::where('id', '!=', Auth::id())->get();
        $data['authInfo'] = User::find(Auth::id());

        return view('layouts.bill', $data);
    }

    public function transaction(Request $request)
    {
        $user       = User::find(Auth::id());
        $transaction = new Transaction();
        $transaction->nama          = $request->fpx_sellerExOrderNo;
        $transaction->description   = $request->fpx_sellerOrderNo;
        $transaction->transac_no    = $request->fpx_fpxTxnId;
        $transaction->datetime_created = now();
        $transaction->amount        = $request->fpx_txnAmount;
        $transaction->status        = 'Pending';
        $transaction->email         = $request->fpx_buyerEmail;
        $transaction->telno         = $request->telno;
        $transaction->username      = $request->fpx_buyerName;
        $transaction->fpx_checksum  = $request->fpx_checkSum;

        if ($user) {
            $transaction->user_id   = Auth::id();
        }
        if ($transaction->save()) {
            $id = substr($request->fpx_sellerOrderNo, -1);
            $transaction->donation()->attach($id, ['payment_type_id' => 1]);

            /// ******************* utk bridge yuran ****************************

            // dd('done');
            // return view('fpx.tStatus', compact('request', 'user'));
        }
    }

    public function successPay()
    {
        return view('fpx.transactionFailed');
        return view('pentadbir.fee.successpay');
    }

    public function fpxIndex(Request $request)
    {
        // dd($request);
        // $user = Auth::id();
        $user       = User::find(Auth::id());

        if ($request->desc == 'Donation') {
            $fpx_buyerEmail = $request->email;
            $telno = $request->telno;
            $fpx_buyerName = $request->name;
            $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
        // $fpx_buyerIban      = $request->name . "/" . $telno . "/" . $request->email;
        } else {
            $fpx_buyerEmail       = "prim.utem@gmail.com";
            $telno               = $user->telno;
            $fpx_buyerName       = User::where('id', '=', Auth::id())->pluck('name')->first();
            $fpx_sellerExOrderNo = $request->desc . "_" . date('YmdHis');
            // $fpx_buyerIban      = "";
        }

        $fpx_msgType        = "AR";
        $fpx_msgToken       = "01";
        $fpx_sellerExId     = "EX00011125";
        $fpx_sellerTxnTime  = date('YmdHis');
        $fpx_sellerOrderNo  = date('YmdHis') . rand(10000, 99999)  . $request->o_id;
        $fpx_sellerId       = "SE00045101";
        $fpx_sellerBankCode = "01";
        $fpx_txnCurrency    = "MYR";
        $fpx_buyerIban      = "";
        $fpx_txnAmount      = $request->amount;
        $fpx_checkSum       = "";
        $fpx_buyerBankId    = $request->bankid;
        $fpx_buyerBankBranch = "";
        $fpx_buyerAccNo     = "";
        $fpx_buyerId        = "";
        $fpx_makerName      = "";
        $fpx_productDesc    = $request->desc;
        $fpx_version        = "6.0";

        /* Generating signing String */
        $data = $fpx_buyerAccNo . "|" . $fpx_buyerBankBranch . "|" . $fpx_buyerBankId . "|" . $fpx_buyerEmail . "|" . $fpx_buyerIban . "|" . $fpx_buyerId . "|" . $fpx_buyerName . "|" . $fpx_makerName . "|" . $fpx_msgToken . "|" . $fpx_msgType . "|" . $fpx_productDesc . "|" . $fpx_sellerBankCode . "|" . $fpx_sellerExId . "|" . $fpx_sellerExOrderNo . "|" . $fpx_sellerId . "|" . $fpx_sellerOrderNo . "|" . $fpx_sellerTxnTime . "|" . $fpx_txnAmount . "|" . $fpx_txnCurrency . "|" . $fpx_version;

        /* Reading key */
        // dd(getenv('FPX_KEY'));
        $priv_key = getenv('FPX_KEY');
        // $priv_key = file_get_contents('C:\\pki-keys\\DevExchange\\EX00012323.key');
        $pkeyid = openssl_get_privatekey($priv_key, null);
        openssl_sign($data, $binary_signature, $pkeyid, OPENSSL_ALGO_SHA1);
        $fpx_checkSum = strtoupper(bin2hex($binary_signature));
        return view('fpx.index', compact(
            'fpx_msgType',
            'fpx_msgToken',
            'fpx_sellerExId',
            'fpx_sellerExOrderNo',
            'fpx_sellerTxnTime',
            'fpx_sellerOrderNo',
            'fpx_sellerId',
            'fpx_sellerBankCode',
            'fpx_txnCurrency',
            'fpx_txnAmount',
            'fpx_buyerEmail',
            'fpx_checkSum',
            'fpx_buyerName',
            'fpx_buyerBankId',
            'fpx_buyerBankBranch',
            'fpx_buyerAccNo',
            'fpx_buyerId',
            'fpx_makerName',
            'fpx_buyerIban',
            'fpx_productDesc',
            'fpx_version',
            'telno',
            'data'
        ));
    }

    public function paymentStatus(Request $request)
    {
        return view('fpx.pStatus', compact('request'));
    }

    public function transactionReceipt(Request $request)
    {
        $case = explode("_", $request->fpx_sellerExOrderNo);
        // $text = explode("/", $request->fpx_buyerIban);

        if ($request->fpx_debitAuthCode == '00') {
            switch ($case[0]) {
                case 'School Fees':
                    $user = Transaction::where('nama', '=', $request->fpx_sellerExOrderNo)->first();
                    $user2 = User::find(Auth::id());
                    $transaction = Transaction::where('nama', '=', $request->fpx_sellerExOrderNo)->first();
                    $transaction->transac_no = $request->fpx_fpxTxnId;
                    $transaction->status = "Success";

                    if ($transaction->save()) {
                        $res = DB::table('fees_transactions')->insert(array(
                            0 => array(
                                'student_fees_id' => 1,
                                'payment_type_id' => 1,
                                'transactions_id' => $transaction->id,
                            ),
                        ));

                        if ($res) {
                            return view('fpx.tStatus', compact('request', 'user'));
                        } else {
                            return view('errors.500');
                        }
                    }
                    break;

                case 'Donation':

                    Transaction::where('nama', '=', $request->fpx_sellerExOrderNo)->update(['transac_no' => $request->fpx_fpxTxnId, 'status' => 'Success']);
                    // $user       = Transaction::where('nama', '=', $request->fpx_sellerExOrderNo)->first();
                    // $user2      = User::find(Auth::id());
                    $donation = $this->donation->getDonationByTransactionName($request->fpx_sellerExOrderNo);
                    $organization = $this->organization->getOrganizationByDonationId($donation->id);
                    $transaction = $this->transaction->getTransactionByName($request->fpx_sellerExOrderNo);

                    Mail::to($transaction->email)->send(new DonationReceipt($donation, $transaction, $organization));

                    return view('receipt.index', compact('request', 'donation', 'organization', 'transaction'));

                    break;
                default:
                    return view('errors.500');
                    break;
            }
            return view('errors.500');
        } else {
            Transaction::where('nama', '=', $request->fpx_sellerExOrderNo)->update(['transac_no' => $request->fpx_fpxTxnId, 'status' => 'Failed']);
            $user = Transaction::where('nama', '=', $request->fpx_sellerExOrderNo)->first();
            return view('fpx.transactionFailed', compact('request', 'user'));
        }
    }

    public function showReceipt()
    {
        // $donation = $this->donation->getDonationByTransactionName("Donation_23210315210448");
        // $organization = $this->organization->getOrganizationByDonationId(3);
        // dd($organization);
        return view('receipt.index');
    }
}
