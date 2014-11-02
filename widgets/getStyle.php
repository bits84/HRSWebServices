<?php


class GetStyle extends CWidget {
    
    protected $_assetsUrl;

    private function _getAssetsUrl() {
        if($this->_assetsUrl !== null) {
            return $this->_assetsUrl;
        } else {
            $assetsPath=Yii::getPathOfAlias('application.extensions.HRSWebServices.widgets.assets');
            $this->_setAssetsUrl($assetsPath);
            return $this->_assetsUrl;
        }
    }
    
    private function _setAssetsUrl($path) {
        if(($assetsPath = realpath($path)) === false || !is_dir($assetsPath) || !is_writable($assetsPath))
            throw new CException(Yii::t('app','Assets path "{path}" is not valid. Please make sure it is a directory writable by the Web server process.',
                array('{path}'=>$path)));
        $assetsUrl = Yii::app()->assetManager->publish($path, false, -1, YII_DEBUG);
        $this->_assetsUrl = $assetsUrl;
    }
    
    public function init() {
        $cs = Yii::app()->clientScript;
        $cs->addPackage('hrs', array(
            'baseUrl' => $this->_getAssetsUrl(),
            'css'     => array( 
                'css/hrs.css'
            ),
            'depends' => array( 
                'jquery' 
            )
        ));
        $cs->registerPackage('hrs');
    }
    
    public function run() {
        
    }
}
?>
