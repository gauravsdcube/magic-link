<?php
/**
 * @copyright Copyright (c) 2026 D Cube Consulting Ltd. All rights reserved.
 * @author D Cube Consulting <info@dcubeconsulting.co.uk>
 */

namespace humhub\modules\magicLinkAuth\controllers;

use humhub\modules\admin\components\Controller;
use humhub\modules\magicLinkAuth\models\SettingsForm;
use Yii;

class ConfigController extends Controller
{
    public function actionIndex()
    {
        $form = new SettingsForm();

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            $this->view->saved();
            return $this->redirect(['/magic-link-auth/config/index']);
        }

        return $this->render('index', ['model' => $form]);
    }
}
