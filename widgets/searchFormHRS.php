<?php
/**
 * Created on 02.05.2013
 * @author Andrey Morozov andrey@3davinci.ru
 */

class SearchFormHRS extends CWidget {
    
    protected $_assetsUrl;
    
    public $type = 'full';
    public $formParams = array();

    private function _getAssetsUrl() {
        if($this->_assetsUrl !== null) {
            return $this->_assetsUrl;
        } else {
            $assetsPath=Yii::getPathOfAlias('application.extensions.hrsWebServices.widgets.assets');
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
            'js'      => array( 
                'js/hrs.js'
            ),
            'depends' => array( 
                'jquery' 
            )
        ));
        $cs->registerPackage('hrs');
        
        if (isset($this->formParams['depart'])) {
            $this->formParams['depart'] = str_replace('.', '', $this->formParams['depart']);
        }

    }
    
    public function run() {
        if ($this->type == 'full') {
            $this->render('searchForm', array(
                'formParams' =>  $this->formParams
            ));
        } else {
            $this->render('searchFormSmall', array(
                'formParams' =>  $this->formParams
            ));
        }
    }



}
?>
