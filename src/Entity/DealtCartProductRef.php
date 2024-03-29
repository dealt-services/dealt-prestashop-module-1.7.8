<?php

declare(strict_types=1);

namespace DealtModule\Entity;

use Cart;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="DealtModule\Repository\DealtCartProductRefRepository")
 */
class DealtCartProductRef
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var DealtOffer
     * @ORM\OneToOne(targetEntity="DealtModule\Entity\DealtOffer")
     * @ORM\JoinColumn(name="id_offer", referencedColumnName="id_offer", nullable=false)
     */
    private $offer;

    /**
     * @var int
     * @ORM\Column(name="id_cart", type="integer")
     */
    private $cartId;

    /**
     * @var int
     * @ORM\Column(name="id_product", type="integer")
     */
    private $productId;

    /**
     * @var int
     * @ORM\Column(name="id_product_attribute", type="integer")
     */
    private $productAttributeId;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return int
     */
    public function setId($id)
    {
        return $this->id = $id;
    }

    /**
     * @return DealtOffer
     */
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     * @param DealtOffer $offer
     *
     * @return DealtCartProductRef
     */
    public function setOffer(DealtOffer $offer)
    {
        $this->offer = $offer;

        return $this;
    }

    /**
     * @return int
     */
    public function getCartId()
    {
        return $this->cartId;
    }

    /**
     * @return Cart
     */
    public function getCart()
    {
        return new Cart($this->cartId);
    }

    /**
     * @param int $cartId
     *
     * @return DealtCartProductRef
     */
    public function setCartId($cartId)
    {
        $this->cartId = $cartId;

        return $this;
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param int $productId
     *
     * @return DealtCartProductRef
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * @return int
     */
    public function getProductAttributeId()
    {
        return $this->productAttributeId;
    }

    /**
     * @param int $productAttributeId
     *
     * @return DealtCartProductRef
     */
    public function setProductAttributeId($productAttributeId)
    {
        $this->productAttributeId = $productAttributeId;

        return $this;
    }
}
