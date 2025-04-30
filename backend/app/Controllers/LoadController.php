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
use App\Models\WishlistModel;
use App\Models\EnrollmentsModel;
use App\Models\NotesModel;
use App\Models\DiscussionsModel;
use App\Controllers\Analytics\Analytics;

use App\Models\ClassesModel;

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
    protected $wishlistModel;
    protected $enrollmentsModel;
    protected $analyticsObject;
    protected $notesModel;
    protected $discussionsModel;
    protected $classesModel;
    public function __construct($model = [])
    {
        // initialize the models
        $this->authModel = new AuthModel();
        $this->usersModel = new UsersModel();
        $this->accessModel = new AccessModel();
        $this->analyticsObject = new Analytics();
        
        // initialize the cache object
        if(empty($this->cacheObject)) {
            $this->cacheObject = new Caching();
        }

        // get the last name of the class that has been called and trigger the model
        $childClass = get_called_class();
        $getLastName = explode('\\', $childClass);
        $triggeredModel = $getLastName[count($getLastName) - 1];

        $this->triggerModel(strtolower($triggeredModel));
    }

    /**
     * Trigger model
     * 
     * @param array $model
     * @return void
     */
    public function triggerModel($model) {
        $models = stringToArray($model);
        
        // Define a mapping of model names to their corresponding model classes
        $modelMap = [
            'categories' => CategoriesModel::class,
            'tags' => TagsModel::class,
            'courses' => CoursesModel::class,
            'instructors' => InstructorsModel::class,
            'wishlist' => WishlistModel::class,
            'reviews' => ReviewsModel::class,
            'enrollments' => EnrollmentsModel::class,
            'notes' => NotesModel::class,
            'discussions' => DiscussionsModel::class,
            'classes' => ClassesModel::class,
        ];
        
        // Loop through the requested models and initialize them
        foreach ($models as $modelName) {
            if (isset($modelMap[$modelName])) {
                $propertyName = $modelName . 'Model';
                $this->{$propertyName} = !empty($this->{$propertyName}) ? $this->{$propertyName} : new $modelMap[$modelName]();
            }
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
