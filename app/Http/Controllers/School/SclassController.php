<?php

namespace App\Http\Controllers\School;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Sclass;
use App\Models\Term;
use App\Models\Club;

class SclassController extends Controller
{
    public function index()
    {
        $schoolsId = \Auth::guard("school")->id();
        return view('school/sclass/index', compact("schoolsId"));
    }

    public function getSclassesData()
    {
        $schoolsId = \Auth::guard("school")->id();
        $sclasses = Sclass::where(['sclasses.schools_id' => $schoolsId])->get();
        return json_encode($sclasses);
    }

    public function getTermsData()
    {
        $schoolsId = \Auth::guard("school")->id();
        $terms = Term::get();
        return json_encode($terms);
    }

    public function createOneSclass(Request $request)
    {
        try {
            $sclass = Sclass::create([
                'enter_school_year' => $request->get('enter_school_year'),
                'class_num' => $request->get('class_num'),
                'class_title' => $request->get('class_title'),
                'is_graduated' => $request->get('is_graduated'),
                'schools_id' => $request->get('schools_id'),
            ]);
        } catch (Exception $e) {
            throw new Exception("Error Processing Request", 1);
        }
    }

    public function changeSclassStatus(Request $request)
    {
        $sclassesId = $request->get('sclassesId');
        try {
            $sclass = Sclass::find($sclassesId);
            $sclass->is_graduated = 
            $sclass = Sclass::create([
                'enter_school_year' => $request->get('enter_school_year'),
                'class_num' => $request->get('class_num'),
                'class_title' => $request->get('class_title'),
                'is_graduated' => $request->get('is_graduated'),
                'schools_id' => $request->get('schools_id'),
            ]);
        } catch (Exception $e) {
            throw new Exception("Error Processing Request", 1);
        }
    }

    public function createOneTerm(Request $request)
    {
        try {
            $term = Term::create([
                'enter_school_year' => $request->get('enter_school_year'),
                'grade_key' => $request->get('grade_key'),
                'term_segment' => $request->get('term_segment'),
                'is_current' => $request->get('is_current'),
                'from_date' => $request->get('from_date'),
                'to_date' => $request->get('to_date'),
            ]);
        } catch (Exception $e) {
            throw new Exception("Error Processing Request", 1);
        }
    }

    public function getClubsData()
    {
        $schoolsId = \Auth::guard("school")->id();
        $clubs = Club::where('schools_id', '=', $schoolsId)->get();
        return json_encode($clubs);
    }

    public function createOneClub(Request $request)
    {
        try {
            $club = Club::create([
                'schools_id' => $request->get('schools_id'),
                'club_title' => $request->get('club_title'),
                'status' => $request->get('status'),
                'term_desc' => $request->get('term_desc'),
            ]);
        } catch (Exception $e) {
            throw new Exception("Error Processing Request", 1);
        }
    }
}
