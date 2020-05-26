<?php

namespace App\Job\JobItems\Repositories\Interfaces;

use Jsdecena\Baserepo\BaseRepositoryInterface;
use App\Job\JobItems\JobItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface JobItemRepositoryInterface extends BaseRepositoryInterface
{ 

    public function listJobItems(string $order = 'id', string $sort = 'desc', array $columns = ['*']) : Collection;

    public function listJobitemCount() : int;

    public function listRecentJobItemId(int $historyFlag = 0);

    public function createRecentJobItemIdList($req, int $id) : void;

    public function baseSearchJobItems(array $searchParam = []); 

    public function getSortJobItems($query, string $order = 'id', string $sort = 'desc', array $columns = ['*']);

    public function findJobItemById($id);

    public function existJobItemImageAndDeleteOnPost(string $imageFlag, int $editFlag = 0);

    public function saveJobItemImages(UploadedFile $file, string $imageFlag) : string;

    public function existJobItemImageAndDeleteOnDelete($imageFlag, int $editFlag = 0, $job = '');

    public function existJobItemMovieAndDeleteOnPost(string $movieFlag, int $editFlag = 0);

    public function saveJobItemMovies(UploadedFile $file, string $movieFlag) : string;

    public function existJobItemMovieAndDeleteOnDelete($movieFlag, int $editFlag = 0, $job = '');

    public function getJobImageBaseUrl() : string;


 
 
}