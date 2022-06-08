<div class="glider-contain">
  <div class="glider">
      {foreach from=$image_array item=ia}
              <div class="product">
                <img src="//{$ia}"/>
                <a href="{$link_array[$i]}"><h5>{$name_array[$i]}</h5></a>
              </div> 
              {$i=$i+1}
          {/foreach}
  </div>

  <button aria-label="Previous" class="glider-prev">«</button>
  <button aria-label="Next" class="glider-next">»</button>
  <div role="tablist" class="dots"></div>
</div>

