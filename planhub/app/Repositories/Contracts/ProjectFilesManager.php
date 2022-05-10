<?php
namespace App\Repositories\Contracts;

/**
 *
 */
interface ProjectFilesManager
{

    public function checkProjectMerged($data, $userData);
    public function getsortedFiles($data, $UserData);


}