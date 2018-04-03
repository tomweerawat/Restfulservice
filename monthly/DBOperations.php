<?php

class DBOperations
{
    private $host = '127.0.0.1:3306';
    private $user = 'hotum';
    private $db = 'hotum';
    private $pass = '';
    // private $host = 'localhost';
    // private $user = 'root';
    // private $db = 'findhouse';
    // private $pass = '1234';
    private $conn;

    public function __construct()
    {
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db, $this->user, $this->pass);
            $this->conn->exec("set names utf8");
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage();
            die();
        }

    }


    public function getDataSeperateMrchnt($startdate, $enddate)
    {
        $sql = " Select
        trns.trns_dttm,
        mer.mrchnt_name,
        srvc.srvc_name,
        trns.unit,
        vat_type_desc(mer.vat_type),
        trns.mrchnt_incm_amnt,
        trns.mrchnt_rev_amnt,
        trns.mrchnt_net_incm_amnt,
        rt.fee_incv_amnt,
        rt.disc_amnt,
        rt.coin_amnt,
        (rt.disc_amnt+rt.coin_amnt),
        mer.mrchnt_owner_name,
        mer.mrchnt_tax_id,
        CONCAT(ba.addr_1,' ',ba.addr_2,' ',ba.post_code),
        cus.cust_frst_name,
        cus.cust_last_name
		from
        mrchnt_srvc_trns trns
        inner join mrchnt mer
        on trns.mrchnt_id=mer.mrchnt_id
        inner join rcpt_tax rt
        on rt.rcpt_id=trns.rcpt_id and rt.rcpt_stts='P'
        inner join mrchnt_srvc srvc
        on trns.srvc_id=srvc.srvc_id
        inner join bill_addr ba
        on ba.bill_addr_id=mer.bill_addr_id
        inner join cust cus
        on trns.cust_id=cus.cust_id
        where
        date(trns.trns_dttm)>='$startdate'
    and date(trns.trns_dttm)<='$enddate'
order by substring(mer.mrchnt_id,-3)asc,trns.trns_dttm asc";


        $query = $this->conn->query($sql);

        $result [ 'property' ] = [];

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            /* var_export($row);exit();*/
            $result2[ 'transaction date time' ] = $row[ 'trns_dttm' ];
            $result2[ 'Merchant' ] = $row[ 'mrchnt_name' ];
            $result2[ 'Service' ] = $row[ 'srvc_name' ];
            $result2[ 'unit' ] = $row[ 'unit' ];
            $result2[ 'VAT Case' ] = $row[ 'vat_type_desc(mer.vat_type)' ];
            $result2[ 'Income from cust' ] = $row[ 'mrchnt_incm_amnt' ];
            $result2[ 'commission from merchant' ] = $row[ 'mrchnt_rev_amnt' ];
            $result2[ 'merchant net income' ] = $row[ 'mrchnt_net_incm_amnt' ];
            $result2[ 'fee include vat' ] = $row[ 'fee_incv_amnt' ];
            $result2[ 'discount from code' ] = $row[ 'disc_amnt' ];
            $result2[ 'coin used' ] = $row[ 'coin_amnt' ];
            $result2[ 'Total discount' ] = $row[ 'disc_amnt' ] + $row[ 'coin_amnt' ];
            $result2[ 'Name of tax' ] = $row[ 'mrchnt_owner_name' ];
            $result2[ 'Tax No.' ] = $row[ 'mrchnt_tax_id' ];
            $result2[ 'bill address' ] = $row[ 'CONCAT(ba.addr_1,\' \',ba.addr_2,\' \',ba.post_code)' ];
            $result2[ 'CustName' ] = $row[ 'cust_frst_name' ];
            $result2[ 'CustLastName' ] = $row[ 'cust_last_name' ];

            array_push($result[ 'property' ], $result2);

        }
        return $result;

    }

    public function dataSumMrchnt($startdate, $enddate)
    {
        $sql = "
select
	$startdate,
    $enddate,
    mer.mrchnt_name,
    srvc.srvc_name,
    a.unit,
    vat_type_desc(mer.vat_type),
    a.mrchnt_incm_amnt,
    a.mrchnt_incm_amnt_exvat,
    a.mrchnt_incm_amnt_vat,
    a.mrchnt_rev_amnt,
    a.mrchnt_net_incm_amnt,
    a.fee_incv_amnt,
    a.disc_amnt,
    a.coin_amnt,
    a.disc_amnt + a.coin_amnt,
    mer.mrchnt_owner_name,
    mer.mrchnt_tax_id,
    mer.mrchnt_bank,
    mer.mrchnt_bank_acct,
    mer.mrchnt_bank_acct_name,
    mer.mrchnt_bank_banch,
    CONCAT(ba.addr_1,' ',ba.addr_2,' ',ba.post_code),
    mer.mrchnt_cust_mail
from
(
	select
		t.mrchnt_id							as mrchnt_id,
		t.srvc_id							as srvc_id,
		sum(t.unit)							as unit,
		sum(t.mrchnt_incm_amnt)			as mrchnt_incm_amnt,
		sum(t.mrchnt_incm_amnt_exvat)		as mrchnt_incm_amnt_exvat,
		sum(t.mrchnt_incm_amnt_vat)		as mrchnt_incm_amnt_vat,
		sum(t.mrchnt_rev_amnt)				as mrchnt_rev_amnt,
		sum(t.mrchnt_net_incm_amnt)		as mrchnt_net_incm_amnt,
		sum(t.fee_incv_amnt)				as fee_incv_amnt,
		sum(t.disc_amnt)					as disc_amnt,
		sum(t.coin_amnt)					as coin_amnt
	from (
		select
			trns.mrchnt_id					as mrchnt_id,
			trns.srvc_id					as srvc_id,
			trns.unit						as unit,
			trns.mrchnt_incm_amnt 			as mrchnt_incm_amnt,
			trns.mrchnt_incm_amnt * (100/107) 	as mrchnt_incm_amnt_exvat,
			trns.mrchnt_incm_amnt - (trns.mrchnt_incm_amnt * (100/107) ) as mrchnt_incm_amnt_vat,
			trns.mrchnt_rev_amnt as mrchnt_rev_amnt,
			trns.mrchnt_net_incm_amnt 				as mrchnt_net_incm_amnt,
			case when trns.rev_share_type = 'P' then trns.rev_share_amnt else 1 end as rev_share_amnt,
			rt.fee_incv_amnt				as fee_incv_amnt,
			rt.disc_amnt					as disc_amnt,
			rt.coin_amnt					as coin_amnt,
			trns.trns_dttm					as 'transaction date time'
		from
			mrchnt_srvc_trns trns
			inner join rcpt_tax rt
			on rt.rcpt_id = trns.rcpt_id AND rt.rcpt_stts = 'P'
		where 
			date(trns.trns_dttm) >= '$startdate'
			and date(trns.trns_dttm) <= '$enddate'

	) t
	group by 
		t.mrchnt_id,t.srvc_id
) a
	inner join mrchnt mer
    on a.mrchnt_id = mer.mrchnt_id
    inner join mrchnt_srvc srvc
    on a.srvc_id = srvc.srvc_id
    inner join bill_addr ba
    on ba.bill_addr_id = mer.bill_addr_id
order by
	substring(mer.mrchnt_id,-3) asc, srvc.srvc_name asc";


        $query = $this->conn->query($sql);

        $result [ 'property' ] = [];

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            /* var_export($row);exit();*/
            $result2[ 'Startdate' ] = $startdate;
            $result2[ 'Enddate' ] = $enddate;
            $result2[ 'Merchant' ] = $row[ 'mrchnt_name' ];
            $result2[ 'Service' ] = $row[ 'srvc_name' ];
            $result2[ 'Unit Time' ] = $row[ 'unit' ];
            $result2[ 'VAT Case' ] = $row[ 'vat_type_desc(mer.vat_type)' ];
            $result2[ 'Merchant Income' ] = $row[ 'mrchnt_incm_amnt' ];
            $result2[ 'Service Value' ] = $row[ 'mrchnt_incm_amnt_exvat' ];
            $result2[ 'VAT' ] = $row[ 'mrchnt_incm_amnt_vat' ];
            $result2[ 'Commission from Merchant' ] = $row[ 'mrchnt_rev_amnt' ];
            $result2[ 'Merchant Net Income' ] = $row[ 'mrchnt_net_incm_amnt' ];
            $result2[ 'FEE' ] = $row[ 'fee_incv_amnt' ];
            $result2[ 'Discount from code' ] = $row[ 'disc_amnt' ];
            $result2[ 'Discount from coin' ] = $row[ 'coin_amnt' ];
            $result2[ 'Total Discount' ] = $row[ 'disc_amnt' ] + $row[ 'coin_amnt' ];
            $result2[ 'Name of tax' ] = $row[ 'mrchnt_owner_name' ];
            $result2[ 'Tax No.' ] = $row[ 'mrchnt_tax_id' ];
            $result2[ 'Bank' ] = $row[ 'mrchnt_bank' ];
            $result2[ 'Bank account no.' ] = $row[ 'mrchnt_bank_acct' ];
            $result2[ 'Bank account name' ] = $row[ 'mrchnt_bank_acct_name' ];
            $result2[ 'Bank banch' ] = $row[ 'mrchnt_bank_banch' ];
            $result2[ 'bill address' ] = $row[ 'CONCAT(ba.addr_1,\' \',ba.addr_2,\' \',ba.post_code)' ];
            $result2[ 'Mail' ] = $row[ 'mrchnt_cust_mail' ];

            array_push($result[ 'property' ], $result2);

        }
        return $result;

    }
}