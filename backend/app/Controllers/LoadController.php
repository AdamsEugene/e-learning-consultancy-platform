<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\AccessModel;
use App\Models\AuthModel;
use App\Libraries\Routing;
use App\Libraries\Caching;
use App\Models\CategoriesModel;
use App\Models\TagsModel;
use App\Models\CoursesModel;
use App\Models\InstructorsModel;
use App\Models\ReviewsModel;

class LoadController extends BaseController
{
    public $restrictedDomain = ['e-learning.com', 'e-learning.com'];
    
    protected $usersModel;
    protected $accessModel;
    protected $authModel;
    protected $accountStatus;
    protected $categoriesModel;
    protected $tagsModel;
    protected $coursesModel;
    protected $instructorsModel;
    protected $reviewsModel;

    public function __construct($model = [])
    {
        // initialize the models
        $this->authModel = new AuthModel();
        $this->usersModel = new UsersModel();
        $this->accessModel = new AccessModel();

        // initialize the cache object
        if(empty($this->cacheObject)) {
            $this->cacheObject = new Caching();
        }
    }

    /**
     * Trigger model
     * 
     * @param array $model
     * @return void
     */
    public function triggerModel($model) {
        if(in_array('categories', $model)) {
            $this->categoriesModel = new CategoriesModel();
        }

        if(in_array('tags', $model)) {
            $this->tagsModel = new TagsModel();
        }

        if(in_array('courses', $model)) {
            $this->coursesModel = new CoursesModel();
        }

        if(in_array('instructors', $model)) {
            $this->instructorsModel = new InstructorsModel();
        }

        if(in_array('reviews', $model)) {
            $this->reviewsModel = new ReviewsModel();
        }
    }

    /**
     * Tracking in progress
     * 
     * @return array
     */
    public function trackingInProgress() {
        return Routing::success([], [
            'status' => dataTrackingStatuses('begin'),
            'timeToGo' => $this->accountStatus
        ]);
    }

}
