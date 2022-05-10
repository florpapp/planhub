<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\ProjectFilesManager;
use App\Http\Requests;

class GetDocumentsController extends Controller
{
    
    protected $projectFilesManager = null;
   
    public function __construct(ProjectFilesManager $projectFilesManager)
    {
        $this->projectFilesManager = $projectFilesManager;
    }

    public function getProjectDocumentSorted(Request $request)
    {
        try {
            
            $inputData = $request->input();
            $userData = $request->user;

            $request->validate([
                'pageNumber' => 'required',
                'projectId' => 'required',
                'sortBy' => 'required|varcha(100)',
                'orderBy' => 'required|varcha(100)'
            ]);//validation methods if on error they throw an exception

            $userData->validate([
                'email' => 'required',
                'is_user' => 'required'

            ]);//validation methods if on error they throw an exception

            $data['pageNumber'] = $inputData['pageNumber'];
            $data['projectId'] = $inputData['projectId'];
            $data['orderBy'] = $inputData['orderBy'];
            $sort_by = $inputData['sortedBy'];

            $data['showFilesForGcComp'] = isset($inputData['showFilesForGcComp']) ? $inputData['showFilesForGcComp'] : null;
            $data['limit'] = isset($inputData['limit']) ? $inputData['limit'] : 10;
            $data['skip'] = ($data['pageNumber'] - 1) * ($data['limit']);
            $data['planSubTypes'] = isset($inputData['planSubTypes']) ? $inputData['planSubTypes'] : null;
            $data['docType'] = isset($inputData['docType']) ? $inputData['docType'] : null;

            switch ($sort_by){
                case 'docType':
                    $data['sortedBy'] = 'type';
                break;
                case 'planTypes':
                    $data['sortedBy'] = 'plan_type';
                break;
                case 'file_extension':
                    $data['sortedBy'] = 'file_extension';
                break;
                case 'created_at':
                    $data['sortedBy'] = 'created_at';
                break;
                default: //sorted by name also here
                    $data['sortedBy'] = 'name';
                break;
            }
            
            /* 
            * If the user is a General Contractor check if the project is merged and return the new data
            */
            
            if (isset($userData['is_user']) && ($userData['is_user'] == 'General Contractor')) {
               $data = $this->projectFilesManager->checkProjectMerged($data, $userData);
            }
            $result = $this->projectFilesManager->getsortedFiles($data, $userData);
            /* 
            * Returns all the data and the count of the files type
            */
            return $result;

        } catch (\Exception $e) {
            //there could be a tailored class for errors and manage them depending on what the views handle
            $error = $e->getMessage();
            return $error;
        }
    }
    
}
