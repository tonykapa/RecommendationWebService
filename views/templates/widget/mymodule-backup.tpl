<section class="swiper mySwiper">
  <h1>{l s='Προτεινόμενα !' d='Modules.Bestsellers.Shop'}</h1>
  <div class="products swiper-wrapper" style="display:flex;">
    {foreach from=$products item="product"}
      {include file="catalog/_partials/miniatures/product-widget.tpl" product=$product}
    {/foreach}
  </div>
  <button aria-label="Previous" class="glider-prev">«</button>
  <button aria-label="Next" class="glider-next">»</button>
  <div role="tablist" class="dots"></div>
</section>