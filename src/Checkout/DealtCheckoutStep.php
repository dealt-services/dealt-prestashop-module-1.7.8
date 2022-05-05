<?php

declare(strict_types=1);

namespace DealtModule\Checkout;

use AbstractCheckoutStepCore;
use Address;
use Context;
use DealtModule\Entity\DealtCartProductRef;
use DealtModule\Entity\DealtOffer;
use DealtModule\Presenter\DealtOfferPresenter;
use DealtModule\Repository\DealtCartProductRefRepository;
use DealtModule\Repository\DealtOfferRepository;
use DealtModule\Service\DealtAPIService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class DealtCheckoutStep extends AbstractCheckoutStepCore
{
    protected $template = 'module:dealtmodule/views/templates/front/checkout/dealt-step.tpl';

    /** @var DealtAPIService */
    private $apiService;

    /** @var DealtOfferRepository */
    private $offerRepository;

    /** @var DealtCartProductRefRepository */
    private $dealtCartRefRepository;

    /** @var DealtOfferPresenter */
    private $offerPresenter;

    /** @var array<mixed> */
    private $offers = [];

    /** @var bool */
    private $valid = null;

    /** @var string */
    private $zipCode;

    /** @var string */
    private $country;


    /**
     * @param Context $context
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Context $context,
        TranslatorInterface $translator,
        DealtAPIService $apiService,
        EntityManagerInterface $em,
        DealtOfferPresenter $offerPresenter
    ) {
        parent::__construct($context, $translator);
        $this->apiService = $apiService;
        $this->offerRepository = $em->getRepository(DealtOffer::class);
        $this->dealtCartRefRepository = $em->getRepository(DealtCartProductRef::class);
        $this->offerPresenter = $offerPresenter;
    }

    public function handleRequest(array $requestParameters = [])
    {
        $this->setTitle($this->getTranslator()->trans('Service availability'));
        $checkoutSession = $this->getCheckoutSession();
        if ($this->valid == null) $this->setComplete(false);

        if (($this->isReachable() || intval($checkoutSession->getIdAddressDelivery()) != 0) && !$this->isComplete()) {
            $this->verifyOfferAvailabilityForSession();
            $this->setComplete($this->valid);
            if (!$this->isComplete()) $this->setCurrent(true);
        }
    }

    public function render(array $extraParams = [])
    {
        return $this->renderTemplate(
            $this->getTemplate(),
            $extraParams,
            ['offers' => $this->offers, 'valid' => $this->valid, 'zipCode' => $this->zipCode, 'country' => $this->country]
        );
    }

    /**
     * @return bool
     */
    public function verifyOfferAvailabilityForSession()
    {
        $checkoutSession = $this->getCheckoutSession();

        $cart = $checkoutSession->getCart();
        $offers = $this->offerRepository->getDealtOffersFromCart($cart);

        /** @var DealtCartProductRef[] */
        $dealtCartRefs = $this->dealtCartRefRepository->findBy(['cartId' => $cart->id]);

        $address = new Address($checkoutSession->getIdAddressDelivery());
        $this->zipCode = $address->postcode;
        $this->country = $address->country;

        $valid = true;
        foreach ($offers as $offer) {
            $offerId = $offer->getDealtOfferId();
            $available = $this->apiService->checkAvailability($offerId, $this->zipCode, $this->country);
            $valid = $valid && $available;

            foreach ($dealtCartRefs as $dealtCartRef) {
                $offerRef = $dealtCartRef->getOffer();
                if ($offerRef->getId() == $offer->getId()) {
                    $productId = $dealtCartRef->getProductId();
                    $productAttributeId = $dealtCartRef->getProductAttributeId();
                    $this->offers[] = array_merge(
                        $this->offerPresenter->present($offer, $cart, $productId, $productAttributeId),
                        ["available" => $available]
                    );
                }
            }
        }

        $this->valid = $valid;
        return $this->valid;
    }
}
