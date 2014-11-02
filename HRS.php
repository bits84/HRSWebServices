<?php

/**
 * Created by PhpStorm.
 * User: aleksandrzamaraev
 * Date: 29.01.14
 * Time: 10:03
 */
class HRS
{


    private $wsdl = 'http://iut-service.hrs.com:8080/service/hrs/026/HRSService?wsdl';
    private $_clientKey = '0000000';
    private $_clientPassword = '00000000';
    private $_clientType = '000';

    private $error_code;
    private $error_message;

    public function request($procedure, $data_to_send = array())
    {
        try {
            $commonParameters = new StdClass();
            $commonParameters->credentials = array('clientType' => $this->_clientType, 'clientKey' => $this->_clientKey, 'clientPassword' => $this->_clientPassword);
            $commonParameters->locale = array('language' => array('iso3Language' => 'RUS'), 'iso3Country' => 'EUR', 'isoCurrency' => 'RUB');

            $obj_merged = (object)array_merge((array)$commonParameters, (array)$data_to_send);
            $soap_options = array('trace' => 1, 'exceptions' => 1);
            $_client = new SoapClient($this->wsdl, $soap_options);

            $_return = $_client->$procedure($obj_merged);

            return $_return;

        } catch (SoapFault $exception) {
            $this->error_code = $exception->detail->HRSException->code;
            $this->error_message = $exception->detail->HRSException->message;
        }


    }

    public function ping()
    {
        $data = new StdClass();
        $data->echoData = 'Are you alive';

        return $this->request('ping', $data)->echoData == 'Are you alive' ? true : false;
    }

    public function  getError()
    {
        return $this->error_message;
    }

//получение данных о городе
    public function search_locations($city)
    {
        $data = new StdClass();
        $data->locationName = $city;
        $data->locationLanguage = array('iso3Language' => 'RUS');

        return $this->request("locationSearch", $data);
    }

//поиск  отелей
    public function search_hotels($location_id, $perimeter = 1000, $maxResults = 0, $minAverageRating = 0, $minCategory = 0)
    {
        $searchCriterion = array('locationId' => $location_id, 'perimeter' => $perimeter);
        $locationCriterion = array('locationCriterion' => $searchCriterion, 'hotelNames' => '',
            'minCategory' => $minCategory, 'minAverageRating' => $minAverageRating, 'maxResults' => $maxResults);

        $data = new StdClass();
        $data->searchCriterion = $locationCriterion;
        $this->request("hotelSearch", $data);
    }

//доступные отели
    public function available_hotels($location_id, $from_date, $to_date, $perimeter = 5000, $maxResults = 0, $minCategory = 0, $roomType = "single", $adultCount = 1, $orderKey = "price", $orderDirection = "ascending")
    {
        $searchCriterion = array('locationId' => $location_id, 'perimeter' => $perimeter);
        $locationCriterion = array('locationCriterion' => $searchCriterion, 'minCategory' => $minCategory, 'maxResults' => $maxResults);

        $data = new StdClass();
        $data->searchCriterion = $locationCriterion;

        $data->availCriterion = $this->availCriterion($from_date, $to_date, $roomType, $adultCount);

        $data->orderCriteria = array("orderKey" => $orderKey, "orderDirection" => $orderDirection);


        return $this->request("hotelAvail", $data);
    }

//дитальная информация об отеле
    public function hotel_availabilty($hotel_key, $from_date, $to_date, $roomType, $adultCount)
    {
        $availC = $this->availCriterion($from_date, $to_date, $roomType, $adultCount);
        $data = new StdClass();
        $data->hotelKeys = $hotel_key;
        $data->availCriterion = $availC;
        $data->genericCriteria = array(array('key' => 'returnAmenities', 'value' => 'true'), array('key' => 'returnHotelDescriptions', 'value' => 'true'),
        array('key' => 'returnDistances', 'value' => 'true'), array('key' => 'returnDistanceLocation', 'value' => 'true'));
        return $this->request("hotelDetailAvail", $data);
    }

//бронирование отеля
    public function hotel_reservation($hotel_key, $from_date, $to_date, $offerDetail, $roomCriteria, $creditCard, $firstName, $lastName, $dateOfBirth, $phone, $email)
    {
        $data = new StdClass();
        $data->hotelKey = $hotel_key;
        $data->paymentMode = 'direct';
        $data->reservationMode = 'guaranteed';
        if (isset($creditCard)) {
            $data->creditCard = $creditCard; // array('cardHolder'=>'Robert Smith', 'number'=>'4321432143214327','organisation'=>'VISA','valid'=>'08/14');
        }
        $data->reservationCriterion = array('from' => $from_date, 'to' => $to_date,
            'reservationRoomOfferDetailCriteria' => array('room' => $roomCriteria,
             'reservationPersons' => array('firstName' => $firstName, 'lastName' => $lastName, 'bedType' => 'regularBed'), 'offerDetail' => $offerDetail, 'optionalRebate' => 'true'));
        $data->orderer = array('firstName' => $firstName, 'middleName' => $firstName, 'lastName' => $lastName, 'dateOfBirth' => $dateOfBirth,
            'street' => 'Somestreet 32', 'postalCode' => '10117', 'city' => 'Lostville', 'iso3Country' => 'RUS', 'phone' => $phone, 'email' => $email);
        $data->billingAddress = array('firstName' => $firstName, 'middleName' => $firstName, 'lastName' => $lastName, 'dateOfBirth' => $dateOfBirth,
            'street' => 'Somestreet 32', 'postalCode' => '10117', 'city' => 'Lostville', 'iso3Country' => 'RUS', 'phone' => $phone, 'email' => $email);
        $data->customerNotifcation = array('type' => 'reserved', 'address' => $email);
        return $this->request("hotelReservation", $data);
    }

    //Отмена бронирования
    public function hotel_canceling($reservationProcessKey,$reservationProcessPassword,$email)
    {
        $data = new StdClass();
        $data->reservationProcessKey = $reservationProcessKey;
        $data->reservationProcessPassword = $reservationProcessPassword;
        $data->customerNotifcation = array('type' => 'email', 'address' => $email);
        return $this->request("hotelReservationCancellation", $data);
    }

    //возвращает набор фотографий  об отеле
    public function hotel_pictures($hotel_key)
    {
        $data = new StdClass();
        $data->hotelKeys = $hotel_key;
        $data->pictureConfiguration = array('width' => 200, 'height' => 200, 'frameColor' => 'FFFFFF', 'scaleUpToRequestedSize' => false, 'useHTTP' => false,
            'cropMode' => 'original', 'quality' => 'low');
        return $this->request("hotelPictures", $data);
    }




    private function availCriterion($rom_date, $to_date, $roomType = "single", $adultCount = 1)
    {
        $roomCriteria = array("id" => "1", "roomType" => $roomType, "adultCount" => $adultCount);

        $data = new StdClass();
        $data->from = $rom_date;
        $data->to = $to_date;
        $data->minPrice = true;
        $data->maxPrice = true;
        $data->includeBreakfastPriceToDetermineCheapestOffer = true;
        $data->roomCriteria = $roomCriteria;
        return $data;
    }

    private function log($text)
    {
        $f = fopen(Yii::app()->basePath . "/runtime/HRS.log", "a+");
        $r = ($_REQUEST) ? $_REQUEST : 'no request';
        fputs($f, date("d:m:Y h:i:s") . ' - ' . $text . "\n\n");
        fclose($f);
    }

    public static function getBreakfast($name)
    {
        if ($name == 'exclusive') return 'эксклюзивный';
        if ($name == 'notAvailable') return 'не доступен';
        if ($name == 'inclusive') return 'включительно';
    }

} 