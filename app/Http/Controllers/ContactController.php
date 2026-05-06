<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    //
    function index()
    {

      $TotalContact=Contact::all();

     return response()->json(
        [
         "Contact"=>$TotalContact,
         "Status"=>200
        ]
     );

    }
}
