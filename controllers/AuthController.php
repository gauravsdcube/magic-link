<?php
/**
 * @copyright Copyright (c) 2026 D Cube Consulting Ltd. All rights reserved.
 * @license Proprietary
 */

namespace humhub\modules\magicLinkAuth\controllers;

use humhub\components\access\ControllerAccess;
use humhub\components\Controller;
use humhub\modules\magicLinkAuth\models\forms\RequestMagicLinkForm;
use humhub\modules\magicLinkAuth\Module;
use humhub\modules\magicLinkAuth\services\LoginRedirectService;
use humhub\modules\magicLinkAuth\services\MagicLinkService;
use humhub\modules\user\controllers\AuthController as UserAuthController;
use humhub\modules\user\events\UserEvent;
use humhub\modules\user\models\User;
use humhub\modules\user\services\LinkRegistrationService;
use Yii;
use yii\base\Event;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;

class AuthController extends Controller
{
    /**
     * @inheritdoc
     */
    public $layout = '@humhub/modules/user/views/layouts/main';

    /**
     * @inheritdoc
     */
    public $access = ControllerAccess::class;

    /**
     * @inheritdoc
     */
    protected $doNotInterceptActionIds = ['*'];

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if ($action->id === 'verify' && Yii::$app->request->isPost) {
            throw new MethodNotAllowedHttpException();
        }

        return parent::beforeAction($action);
    }

    /**
     * Request a magic sign-in link.
     */
    public function actionRequest()
    {
        $this->ensureModuleEnabled();

        if (!Yii::$app->user->isGuest) {
            return $this->goBack();
        }

        if (Yii::$app->settings->get('maintenanceMode')) {
            Yii::$app->session->setFlash('error', ControllerAccess::getMaintenanceModeWarningText());
        }

        $model = new RequestMagicLinkForm();

        if ($model->load(Yii::$app->request->post()) && $model->request()) {
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('success_modal', ['model' => $model]);
            }

            return $this->render('success', ['model' => $model]);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('request_modal', ['model' => $model]);
        }

        return $this->render('request', ['model' => $model]);
    }

    /**
     * Verify a magic sign-in link and log the user in.
     *
     * @throws BadRequestHttpException
     * @throws HttpException
     */
    public function actionVerify(?string $token = null, ?string $guid = null)
    {
        $this->ensureModuleEnabled();

        if (!Yii::$app->user->isGuest) {
            return $this->goBack();
        }

        if ($token === null || $guid === null || strlen($token) > 255 || !preg_match('/^[a-zA-Z0-9_-]+$/', $token)) {
            throw new BadRequestHttpException(Yii::t('MagicLinkAuthModule.base', 'Invalid or expired magic link.'));
        }

        $user = User::findOne(['guid' => $guid, 'status' => User::STATUS_ENABLED]);
        if ($user === null) {
            throw new BadRequestHttpException(Yii::t('MagicLinkAuthModule.base', 'Invalid or expired magic link.'));
        }

        $service = new MagicLinkService();
        $record = $service->validateToken($user, $token);
        if ($record === null) {
            throw new BadRequestHttpException(Yii::t('MagicLinkAuthModule.base', 'Invalid or expired magic link.'));
        }

        Event::trigger(
            UserAuthController::class,
            UserAuthController::EVENT_BEFORE_CHECKING_USER_STATUS,
            new UserEvent(['user' => $user]),
        );

        if (Yii::$app->settings->get('maintenanceMode') && !$user->isSystemAdmin()) {
            throw new HttpException(503, ControllerAccess::getMaintenanceModeWarningText());
        }

        if (!$service->consumeToken($record)) {
            throw new BadRequestHttpException(Yii::t('MagicLinkAuthModule.base', 'Invalid or expired magic link.'));
        }

        $duration = 0;
        if ($record->remember_me) {
            $duration = (int)Yii::$app->getModule('user')->loginRememberMeDuration;
        }

        if (!Yii::$app->user->login($user, $duration)) {
            throw new HttpException(500, Yii::t('MagicLinkAuthModule.base', 'Unable to sign you in. Please try again.'));
        }

        $redirectUrl = (new LoginRedirectService())->resolve();

        $linkRegistrationService = LinkRegistrationService::createFromRequest();
        if (
            $linkRegistrationService->isValid()
            && $linkRegistrationService->inviteToSpace(Yii::$app->user->identity)
        ) {
            $redirectUrl = $linkRegistrationService->getSpace()->getUrl();
        }

        $response = $this->redirect($redirectUrl);

        Event::trigger(
            UserAuthController::class,
            UserAuthController::EVENT_AFTER_LOGIN,
            new UserEvent(['user' => Yii::$app->user->identity]),
        );

        return $response;
    }

    protected function ensureModuleEnabled(): void
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('magic-link-auth');
        if (!$module->isEnabled()) {
            throw new HttpException(404);
        }
    }
}
