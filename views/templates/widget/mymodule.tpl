<section class="mySwiperMobi media">
  <h1>{l s='Προτεινόμενα !' d='Modules.Bestsellers.Shop'}</h1>
  <div class="products product-default bounceIn swiper-wrapper" style="display:flex;">
    {foreach from=$products item="product"}
      {include file="catalog/_partials/miniatures/product-widget.tpl" product=$product}
    {/foreach}
  </div>
  <div class="swiper-pagination"></div>
  
</section>