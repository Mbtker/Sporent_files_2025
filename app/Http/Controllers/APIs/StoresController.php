<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoresController extends Controller
{
    use GeneralTrait;

    public function GetStoreCategories(Request $request)
    {
        $Result = DB::table('storecategories')
            ->where('Status', '=', 1)
            ->orderByRaw('Id DESC')
            ->get();

        return $this->returnDate('Data', $Result);
    }

    public function GetStores(Request $request)
    {
        $Result = DB::table('stores')
            ->where('Status', '=', 1)
            ->orderByRaw('Id DESC')
            ->get();

        return $this->returnDate('Data', $Result);
    }

    public function GetProductCategories(Request $request)
    {

        $Result = DB::table('storesproductscategories')
            ->where('StoreId', '=', $request->StoreId)
            ->where('Status', '=', 1)
            ->orderByRaw('Id ASC')
            ->get();

        return $this->returnDate('Data', $Result);
    }

    public function GetProducts(Request $request)
    {
        $IsAllStatus = $request->IsAllStatus;
        $Result = [];

        if($IsAllStatus)
        {
            $Result = DB::table('storesproducts')
                ->where('StoreId', '=', $request->StoreId)
                ->where('IsDeleted', '=', 0)
                ->orderByRaw('Id DESC')
                ->get();

        } else
        {
            $Result = DB::table('storesproducts')
                ->where('StoreId', '=', $request->StoreId)
                ->where('IsDeleted', '=', 0)
                ->where('Status', '=', 1)
                ->orderByRaw('Id DESC')
                ->get();
        }

        return $this->returnDate('Data', $Result);
    }

    public function SendOrder(Request $request)
    {
        $GetStoreId = $request->StoreId;
        $GetUserId = $request->UserId;
        $GetUserAccountTypeId = $request->UserAccountTypeId;
        $GetLocation = $request->Location;
        $GetPaymentMethodId = $request->PaymentMethodId;
        $GetPaymentId = $request->PaymentMethodId;
        $GetPaymentStatus = $request->PaymentStatus;
        $GetDeliveryCost = $request->DeliveryCost;
        $GetTransactionId = $request->TransactionId;

        $Data = [
            'StoreId' => $GetStoreId,
            'UserAccountTypeId' => $GetUserAccountTypeId,
            'UserId' => $GetUserId,
            'Location' => $GetLocation,
            'PaymentMethodId' => $GetPaymentMethodId,
            'PaymentStatus' => $GetPaymentStatus,
            'PaymentId' => $GetPaymentId,
            'DeliveryCost' => $GetDeliveryCost,
            'TransactionId' => $GetTransactionId,
        ];

        $OrderId = DB::table('storesorders')->insertGetId($Data);

        return $this->returnDate('Data', $OrderId);
    }

    public function SendItems(Request $request)
    {
        $GetOrderId = $request->OrderId;
        $GetProductId = $request->ProductId;
        $GetQuntity = $request->Quntity;
        $GetPrice = $request->Price;
        $GetIsTax = $request->IsTax;

        $Data = [
            'OrderId' => $GetOrderId,
            'ProductId' => $GetProductId,
            'Quntity' => $GetQuntity,
            'Price' => $GetPrice,
            'IsTax' => $GetIsTax,
        ];

        DB::table('ordersitems')->insert($Data);

        return $this->returnDate('InsertStatus', true);
    }

    public function GetStoreOrders(Request $request)
    {
        $StoreId = $request->StoreId;
        $OrderStatus= $request->OrderStatus;

        $Result = DB::table('storesorders')
            ->where('StoreId', '=', $StoreId)
            ->where('Status', '=', $OrderStatus)
            ->orderByRaw('Id DESC')
            ->get();

        for($y = 0; $y<count($Result); $y++)
        {
            $GetUserAccountTypeId = $Result[$y]->UserAccountTypeId;
            $GetUserId = $Result[$y]->UserId;

            $TableName = HelperAPIsFun::GetTableName($GetUserAccountTypeId);

            $UserInfo = DB::table($TableName->TableName)
                ->where('Id', '=', $GetUserId)
                ->first();

            $Result[$y]->User = $UserInfo;

            $GetPaymentMethodId = $Result[$y]->PaymentMethodId;

            if($GetPaymentMethodId != 0)
            {
                $PaymentMethodInfo = DB::table('paymentmethod')
                    ->where('Id', '=', $GetPaymentMethodId)
                    ->first();

                $Result[$y]->PaymentMethod = $PaymentMethodInfo;
            }

            $GetPaymentId = $Result[$y]->PaymentId;

            if($GetPaymentId != 0)
            {
                $GetPaymentInfo = DB::table('payments')
                    ->where('Id', '=', $GetPaymentId)
                    ->first();

                $Result[$y]->Payment = $GetPaymentInfo;
            }


            $Result[$y]->CreateDate = HelperAPIsFun::GetAMPMTime($Result[$y]->CreateDate);

        }

        return $this->returnDate('Data', $Result);
    }

    public function GetOrderProducts(Request $request)
    {
        $OrderId = $request->OrderId;

        $Result = DB::table('ordersitems')
            ->where('OrderId', '=', $OrderId)
            ->orderByRaw('Id DESC')
            ->get();

        for($y = 0; $y<count($Result); $y++)
        {
            $GetProductId = $Result[$y]->ProductId;

            $ProductInfo = DB::table('storesproducts')
                ->where('Id', '=', $GetProductId)
                ->first();

            $Result[$y]->Product = $ProductInfo;
        }

        return $this->returnDate('Data', $Result);
    }

    public function CloseOrder(Request $request)
    {
        $OrderId = $request->OrderId;

        DB::table('storesorders')
            ->where('Id', $OrderId)
            ->update(['Status' => 'Close']);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function CreateNewCategory(Request $request)
    {
        $StoreId = $request->StoreId;
        $NameAr = $request->NameAr;
        $NameEn = $request->NameEn;

        $Data = [
            'StoreId' => $StoreId,
            'NameAr' => $NameAr,
            'NameEn' => $NameEn,
        ];

        DB::table('storesproductscategories')->insert($Data);

        return $this->returnDate('InsertStatus', true);
    }

    public function DeleteCategory(Request $request)
    {
        $CategoryId = $request->CategoryId;

        DB::table('storesproductscategories')
            ->where('Id', $CategoryId)
            ->update(['Status' => '0']);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function UpdateCategory(Request $request)
    {
        $CategoryId = $request->CategoryId;
        $NameAr = $request->NameAr;
        $NameEn = $request->NameEn;

        DB::table('storesproductscategories')
            ->where('Id', $CategoryId)
            ->update(['NameAr' => $NameAr, 'NameEn' => $NameEn]);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function CreateNewProduct(Request $request)
    {
        $StoreId = $request->StoreId;
        $CategoryId = $request->CategoryId;
        $NameAr = $request->NameAr;
        $NameEn = $request->NameEn;
        $Image = $request->Image;
        $Price = $request->Price;

        $Data = [
            'StoreId' => $StoreId,
            'CategoryId' => $CategoryId,
            'NameAr' => $NameAr,
            'NameEn' => $NameEn,
            'Image' => $Image,
            'Price' => $Price,
        ];

        DB::table('storesproducts')->insert($Data);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function UpdateProduct(Request $request)
    {
        $ProductId = $request->ProductId;
        $CategoryId = $request->CategoryId;
        $NameAr = $request->NameAr;
        $NameEn = $request->NameEn;
        $Image = $request->Image;
        $Price = $request->Price;

        $Data = [
            'CategoryId' => $CategoryId,
            'NameAr' => $NameAr,
            'NameEn' => $NameEn,
            'Image' => $Image,
            'Price' => $Price,
        ];

        DB::table('storesproducts')
            ->where('Id', $ProductId)
            ->update($Data);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function UpdateProductStatus(Request $request)
    {
        $ProductId = $request->ProductId;
        $Status = $request->Status;

        $Data = [
            'Status' => $Status,
        ];

        DB::table('storesproducts')
            ->where('Id', $ProductId)
            ->update($Data);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function DeleteProduct(Request $request)
    {
        $ProductId = $request->ProductId;

        $Data = [
            'IsDeleted' => 1,
        ];

        DB::table('storesproducts')
            ->where('Id', $ProductId)
            ->update($Data);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function GetStoreInfo(Request $request)
    {
        $Result = DB::table('stores')
            ->where('Id', '=', $request->StoreId)
            ->first();

        return $this->returnDate('Data', $Result);
    }

    public function UpdateStoreInfo(Request $request)
    {
        $StoreId = $request->StoreId;
        $FirstName = $request->FirstName;
        $LastName = $request->LastName;
        $StoreName = $request->StoreName;
        $CR = $request->CR;
        $VAT = $request->VAT;
        $Location = $request->Location;
        $Logo = $request->Logo;

        $Data = [
            'FirstName' => $FirstName,
            'LastName' => $LastName,
            'Name' => $StoreName,
            'CR' => $CR,
            'IsTax' => $VAT,
            'Location' => $Location,
            'Logo' => $Logo,
        ];

        DB::table('stores')
            ->where('Id', $StoreId)
            ->update($Data);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function GetUserOrders(Request $request)
    {
        $UserId = $request->UserId;
        $AccountTypeId = $request->AccountTypeId;

        $Result = DB::table('storesorders')
            ->where('UserId', '=', $UserId)
            ->where('UserAccountTypeId', '=', $AccountTypeId)
            ->where('IsDelete', '=', 0)
            ->orderByRaw('Id DESC')
            ->get();

        for($y = 0; $y<count($Result); $y++)
        {
            $GetStoreId = $Result[$y]->StoreId;

            $StoreInfo = DB::table('stores')
                ->where('Id', '=', $GetStoreId)
                ->first();

            $Result[$y]->StoreInfo = $StoreInfo;


            $GetPaymentMethodId = $Result[$y]->PaymentMethodId;

            if($GetPaymentMethodId != 0)
            {
                $PaymentMethodInfo = DB::table('paymentmethod')
                    ->where('Id', '=', $GetPaymentMethodId)
                    ->first();

                $Result[$y]->PaymentMethod = $PaymentMethodInfo;
            } else
            {
                $Result[$y]->PaymentMethod = null;
            }

            $GetPaymentId = $Result[$y]->PaymentId;

            if($GetPaymentId != 0)
            {
                $GetPaymentInfo = DB::table('payments')
                    ->where('Id', '=', $GetPaymentId)
                    ->first();

                $Result[$y]->Payment = $GetPaymentInfo;
            } else
            {
                $Result[$y]->Payment = null;
            }

            $GetOrdersItems = DB::table('ordersitems')
                ->where('OrderId', '=', $Result[$y]->Id)
                ->get();

            for($n = 0; $n<count($GetOrdersItems); $n++)
            {
                $GetProductId = $GetOrdersItems[$n]->ProductId;

                $GetOrdersItems[$n]->ProductInfo = DB::table('storesproducts')
                    ->where('Id', '=', $GetProductId)
                    ->first();

                $GetOrdersItems[$n]->ProductInfo->Image = '';
            }

            $Result[$y]->OrdersItems = $GetOrdersItems;

            $Result[$y]->CreateDate = HelperAPIsFun::GetAMPMTime($Result[$y]->CreateDate);
        }

        return $this->returnDate('Data', $Result);
    }
}
