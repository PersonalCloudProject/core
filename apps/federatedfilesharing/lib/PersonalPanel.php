<?php

namespace OCA\FederatedFileSharing;

use OCP\IL10N;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\IUserSession;
use OCP\Settings\ISettings;
use OCP\Template;

class PersonalPanel implements ISettings {

	/** @var IL10N  */
	protected $l;

	/** @var IUserSession  */
	protected $userSession;

	/** @var IURLGenerator  */
	protected $urlGenerator;

	/** @var FederatedShareProvider */
	protected $shareProvider;

	/** @var IRequest  */
	protected $request;

	public function __construct(
		IL10N $l,
		IUserSession $userSession,
		IURLGenerator $urlGenerator,
		FederatedShareProvider $shareProvider,
		IRequest $request) {
		$this->l = $l;
		$this->userSession = $userSession;
		$this->urlGenerator = $urlGenerator;
		$this->shareProvider = $shareProvider;
		$this->request = $request;
	}

    public function getPriority() {
        return 0;
    }

    public function getSectionID() {
        return 'general';
    }

    public function getPanel() {
		$isIE8 = false;
		preg_match('/MSIE (.*?);/', $this->request->getHeader('User-Agent'), $matches);
		if (count($matches) > 0 && $matches[1] <= 9) {
			$isIE8 = true;
		}
		$cloudID = $this->userSession->getUser()->getCloudId();
		$url = 'https://owncloud.org/federation#' . $cloudID;
		$ownCloudLogoPath = $this->urlGenerator->imagePath('core', 'logo-icon.svg');
		$tmpl = new Template('federatedfilesharing', 'settings-personal');
		$tmpl->assign('outgoingServer2serverShareEnabled', $this->shareProvider->isOutgoingServer2serverShareEnabled());
		$tmpl->assign('message_with_URL', $this->l->t('Share with me through my #ownCloud Federated Cloud ID, see %s', [$url]));
		$tmpl->assign('message_without_URL', $this->l->t('Share with me through my #ownCloud Federated Cloud ID', [$cloudID]));
		$tmpl->assign('owncloud_logo_path', $ownCloudLogoPath);
		$tmpl->assign('reference', $url);
		$tmpl->assign('cloudId', $cloudID);
		$tmpl->assign('showShareIT', !$isIE8);
		$tmpl->assign('urlGenerator', $this->urlGenerator);
		return $tmpl;
    }

}
