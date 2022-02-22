<?php

function clear_price_mask(string $price) {
  if (empty($price) || $price == '') 
    return 0;
  return (float) str_replace(['.', '₺', ','], ['', '', '.'], $price);
}