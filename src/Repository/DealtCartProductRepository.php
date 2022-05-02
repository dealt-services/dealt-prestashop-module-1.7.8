<?php

declare(strict_types=1);

namespace DealtModule\Repository;

use DealtModule\Entity\DealtCartProduct;
use DealtModule\Entity\DealtOffer;
use Doctrine\ORM\EntityRepository;

/**
 * Doctrine DealtOfferCategory repository class
 */
class DealtCartProductRepository extends EntityRepository
{
  /**
   * Creates a Dealt Cart Product Offer
   * 
   * @param int $cartId
   * @param int $productId
   * @param DealtOffer $dealtOffer
   * 
   * @return DealtCartProduct
   */
  public function create($cartId, $productId, $dealtOffer)
  {
    $em = $this->getEntityManager();

    /* ensure a combination of cartId x productId x dealtProductId does not already exist */
    $match = $this->findOneBy(['cartId' => $cartId, 'productId' => $productId, 'offer' => $dealtOffer]);
    if ($match != null) return $match;

    $cartProductOffer = (new DealtCartProduct())
      ->setCartId($cartId)
      ->setProductId($productId)
      ->setOffer($dealtOffer);

    $em->persist($cartProductOffer);
    $em->flush();

    return $cartProductOffer;
  }
}