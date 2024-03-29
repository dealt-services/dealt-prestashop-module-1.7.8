import { debounce } from '../utils';
import { DealtPSClient } from './api/dealt.ps.client';
import { DealtCart } from './cart/dealt.cart';
import { zipcodeAutocomplete } from './utils/zipcode.autocomplete';

const client = new DealtPSClient();
const cart = new DealtCart();
const ps = window.prestashop;

const dealtProductPage = () => {
  $("#dealt-offer-card").removeClass("loading");

  /* selectors */
  const $input = $("#dealt-zipcode-autocomplete");
  const $submit = $("#dealt-offer-submit");
  const $error = $("#dealt-offer-error");
  const $form = $submit.closest("form");

  $error.hide();

  /* Attributes parsing */
  const offerId = $submit.attr("data-dealt-offer-id");
  const hasCartProduct = Boolean($submit.attr("data-dealt-cart-product"));
  const productId = parseInt($submit.attr("data-dealt-product-id"), 10);
  const productAttributeId = parseInt(
    $submit.attr("data-dealt-product-attribute-id"),
    10
  );

  /* setup zipCode autocomplete */
  const destroyAutoComplete = zipcodeAutocomplete($input, $submit);

  $submit.on("click", (e) => {
    e.preventDefault();
    $error.hide();
    $submit.prop("disabled", true);
    const zipCode = $input.val();

    /**
     * Offer attachment flow :
     * - offer must be available
     * - if attached product is not in cart - add it first
     * - add offer to cart to correct productId/attributId pair
     */
    client
      .offerAvailability(offerId, zipCode)
      .then(({ result, ok, error }) =>
        ok && result.available
          ? hasCartProduct
            ? Promise.resolve({ ok: true })
            : cart.psAddToCart($form)
          : Promise.reject(ok && !result.available ? result.reason : error)
      )
      .then(({ ok, error }) =>
        ok
          ? cart.attachOfferToCart(productId, productAttributeId, offerId)
          : Promise.reject(error)
      )
      .then(() =>
        /**
         * if ps_shoppingcart module is activated, this will
         * trigger its ajax cart modal to appear
         */
        ps.emit("updateCart", {
          reason: {
            idCustomization: 0,
            idProduct: productId,
            idProductAttribute: productAttributeId,
            linkAction: "add-to-cart",
          },
          resp: { success: true },
        })
      )
      .catch((e) => {
        $error.text(
          e instanceof Error
            ? e.message
            : typeof e === "object"
            ? JSON.stringify(e)
            : e
        );
        $error.show();
      })
      .finally(() => $submit.prop("disabled", false));
  });

  return () => {
    $submit.off();
    destroyAutoComplete();
  };
};

/**
 * Product page may be replaced on product combination
 * update -> reset listeners after he DOM tree has been replaced
 */
window.$(() => {
  let cleanup = dealtProductPage();
  const onUpdateProduct = debounce(() => {
    cleanup();
    cleanup = dealtProductPage();
  }, 1200);

  prestashop.on("updateProduct", () =>
    $("#dealt-offer-card").addClass("loading")
  );

  prestashop.on("updatedProduct", () => {
    onUpdateProduct();
    $("#dealt-offer-card").removeClass("loading");
  });
});
