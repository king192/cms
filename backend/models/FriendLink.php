<?php
/**
 * Ahthor: lf
 * Email: job@feehi.com
 * Blog: http://blog.feehi.com
 * Date: 2016/4/711:37
 */
namespace backend\models;

use yii;
use yii\behaviors\TimestampBehavior;

class FriendLink extends \common\models\FriendLink
{
    
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function beforeSave($insert)
    {
        if(isset($_FILES['FriendLink']['name']['image']) && $_FILES['FriendLink']['name']['image'] != ''){
            if(!$insert) {
                if(!empty($this->oldAttributes['image'])) {
                    $fileUsageModel = new FileUsage();
                    $fileUsageModel->cancelUseFile($this->oldAttributes['image'], $this->id, FileUsage::TYPE_FRINEDLYLINK);
                }
            }
            $model = new File();
            if ( is_string($uri = $model->saveFile(FileUsage::TYPE_FRINEDLYLINK)) ) {
                $this->image = $uri;
                return true;
            } else {
                $this->addError( 'image', yii::t('app', 'Upload {attribute} error', ['attribute' => yii::t('app', 'Image')]).': '.$uri[0] );
                return false;
            }
        }
        if($this->image == '') unset($this->image);
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterSave($insert, $changedAttributes)
    {
        if( isset($this->image) ){
            $fileUsageModel = new FileUsage();
            $fileUsageModel->useFile($this->image, $this->id, FileUsage::TYPE_FRINEDLYLINK);
        }
        return true;
    }

    public function beforeDelete()
    {
        if( !empty($this->image) ) {
            $fileUsageModel = new FileUsage();
            $fileUsageModel->cancelUseFile($this->image, $this->id, FileUsage::TYPE_FRINEDLYLINK);
        }
        return true;
    }
}