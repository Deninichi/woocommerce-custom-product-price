<div class="pricing-popup" style="display: none">
	<div class="close">x</div>
	<div class="price">Price: <?php echo get_woocommerce_currency_symbol() . '<span>' . $product->get_price() . '</span>'; ?></div>
	<input type="number" name="width" placeholder="Width">
	<input type="number" name="height" placeholder="Height">
	<button class="calculate">Calculate</button>
	<button class="add-to-cart" style="display:none">Add to cart</button>
	<input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
</div>
<div class="pricing-popup-overlay" style="display: none"></div>