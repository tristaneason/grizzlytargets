'use strict';

(function($) {
  var woosb_timeout = null;

  $(function() {
    // ready
    if (!$('.woosb-wrap').length) {
      return;
    }

    $('.woosb-wrap').each(function() {
      woosb_init($(this));
    });
  });

  $(document).on('woosq_loaded', function() {
    // product bundles in quick view popup
    woosb_init($('#woosq-popup .woosb-wrap'));
  });

  $(document).on('woovr_selected', function(e, selected, variations) {
    var $wrap = variations.closest('.woosb-wrap');
    var wrap_id = $wrap.attr('data-id');
    var container = woosb_container(wrap_id);
    var $container = $wrap.closest(container);
    var $products = variations.closest('.woosb-products');
    var $product = variations.closest('.woosb-product');

    if ($product.length) {
      var _id = selected.attr('data-id');
      var _price = selected.attr('data-price');
      var _regular_price = selected.attr('data-regular-price');
      var _price_html = selected.attr('data-pricehtml');
      var _image_src = selected.attr('data-imagesrc');
      var _purchasable = selected.attr('data-purchasable');

      if (_purchasable === 'yes' && _id >= 0) {
        // change data
        $product.attr('data-id', _id);
        $product.attr('data-price', _price);

        // change price
        woosb_change_price($product, _price, _regular_price, _price_html);

        // change image
        if (_image_src && _image_src !== '') {
          $product.find('.woosb-thumb-ori').hide();
          $product.find('.woosb-thumb-new').
              html('<img src="' + _image_src + '"/>').
              show();
        } else {
          $product.find('.woosb-thumb-ori').show();
          $product.find('.woosb-thumb-new').html('').hide();
        }

        // change attributes
        var attrs = {};

        $product.find('select[name^="attribute_"]').each(function() {
          var attr_name = $(this).attr('name');

          attrs[attr_name] = $(this).val();
        });

        $product.attr('data-attrs', JSON.stringify(attrs));
      } else {
        // reset data
        $product.attr('data-id', 0);
        $product.attr('data-price', 0);
        $product.attr('data-attrs', '');

        // reset price
        $product.find('.woosb-price-ori').show();
        $product.find('.woosb-price-new').html('').hide();

        // reset image
        $product.find('.woosb-thumb-ori').show();
        $product.find('.woosb-thumb-new').html('').hide();
      }

      // reset sku
      $container.find('.product_meta .sku').
          text($products.attr('data-product-sku'));
    }

    woosb_init($wrap);
  });

  $(document).on('found_variation', function(e, t) {
    var $wrap = $(e['target']).closest('.woosb-wrap');
    var wrap_id = $wrap.attr('data-id');
    var container = woosb_container(wrap_id);
    var $container = $wrap.closest(container);
    var $products = $(e['target']).closest('.woosb-products');
    var $product = $(e['target']).closest('.woosb-product');

    if ($product.length) {
      if (t['image']['url'] && t['image']['url'] !== '') {
        // change image
        $product.find('.woosb-thumb-ori').hide();

        if (t['image']['srcset'] && t['image']['srcset'] !== '') {
          $product.find('.woosb-thumb-new').
              html('<img src="' + t['image']['url'] + '" srcset="' +
                  t['image']['srcset'] + '"/>').
              show();
        } else {
          $product.find('.woosb-thumb-new').
              html('<img src="' + t['image']['url'] + '"/>').
              show();
        }
      } else {
        $product.find('.woosb-thumb-ori').show();
        $product.find('.woosb-thumb-new').html('').hide();
      }

      if (t['price_html'] !== undefined && t['price_html'] !== '' &&
          t['display_price'] !== undefined && t['display_price'] !== '') {
        woosb_change_price($product, t['display_price'],
            t['display_regular_price'], t['price_html']);
      }

      if (t['variation_description'] !== undefined) {
        $product.find('.woosb-variation-description').
            html(t['variation_description']).
            show();
      } else {
        $product.find('.woosb-variation-description').html('').hide();
      }

      if (t['is_purchasable']) {
        // change the price
        if (woosb_vars.bundled_price_from === 'regular_price' &&
            t['display_regular_price'] !== undefined) {
          $product.attr('data-price', t['display_regular_price']);
        } else {
          $product.attr('data-price', t['display_price']);
        }

        // change stock notice
        if (t['is_in_stock']) {
          $wrap.next('p.stock').show();
          $product.attr('data-id', t['variation_id']);
        } else {
          $wrap.next('p.stock').hide();
          $product.attr('data-id', 0);
        }

        // change availability
        if (t['availability_html'] && t['availability_html'] !== '') {
          $product.find('.woosb-availability').
              html(t['availability_html']).
              show();
        } else {
          $product.find('.woosb-availability').html('').hide();
        }

        // change attributes
        var attrs = {};

        $product.find('select[name^="attribute_"]').each(function() {
          var attr_name = $(this).attr('name');

          attrs[attr_name] = $(this).val();
        });

        $product.attr('data-attrs', JSON.stringify(attrs));
      }

      if (woosb_vars.change_image === 'no') {
        // prevent changing the main image
        $(e['target']).closest('.variations_form').trigger('reset_image');
      }

      // reset sku
      $container.find('.product_meta .sku').
          text($products.attr('data-product-sku'));

      $(document).trigger('woosb_found_variation', [$product, t]);

      woosb_init($wrap);
    }
  });

  $(document).on('reset_data', function(e) {
    var $wrap = $(e['target']).closest('.woosb-wrap');
    var wrap_id = $wrap.attr('data-id');
    var container = woosb_container(wrap_id);
    var $container = $wrap.closest(container);
    var $products = $(e['target']).closest('.woosb-products');
    var $product = $(e['target']).closest('.woosb-product');

    if ($product.length) {
      // reset thumb
      $product.find('.woosb-thumb-new').hide();
      $product.find('.woosb-thumb-ori').show();

      // reset price
      $product.find('.woosb-price-new').hide();
      $product.find('.woosb-price-ori').show();

      // reset availability
      $product.find('.woosb-availability').html('').hide();

      // reset desc
      $product.find('.woosb-variation-description').html('').hide();

      // reset data
      $product.attr('data-id', 0);
      $product.attr('data-price', 0);
      $product.attr('data-attrs', '');

      // reset sku
      $container.find('.product_meta .sku').
          text($products.attr('data-product-sku'));

      $(document).trigger('woosb_reset_data', [$product]);

      woosb_init($wrap);
    }
  });

  $(document).
      on('click touch', '.woosb-qty-input-plus, .woosb-qty-input-minus',
          function() {
            // get values
            var $qty = $(this).closest('.woosb-qty-input').find('.qty'),
                val = parseFloat($qty.val()),
                max = parseFloat($qty.attr('max')),
                min = parseFloat($qty.attr('min')),
                step = $qty.attr('step');

            // format values
            if (!val || val === '' || val === 'NaN') {
              val = 0;
            }

            if (max === '' || max === 'NaN') {
              max = '';
            }

            if (min === '' || min === 'NaN') {
              min = 0;
            }

            if (step === 'any' || step === '' || step === undefined ||
                parseFloat(step) === 'NaN') {
              step = 1;
            } else {
              step = parseFloat(step);
            }

            // change the value
            if ($(this).is('.woosb-qty-input-plus')) {
              if (max && (
                  max == val || val > max
              )) {
                $qty.val(max);
              } else {
                $qty.val((val + step).toFixed(woosb_decimal_places(step)));
              }
            } else {
              if (min && (
                  min == val || val < min
              )) {
                $qty.val(min);
              } else if (val > 0) {
                $qty.val((val - step).toFixed(woosb_decimal_places(step)));
              }
            }

            // trigger change event
            $qty.trigger('change');
          });

  $(document).on('click touch', '.single_add_to_cart_button', function(e) {
    var $this = $(this);

    if ($this.hasClass('woosb-disabled')) {
      e.preventDefault();
    }
  });

  $(document).on('change', '.woosb-qty .qty', function() {
    var $this = $(this);

    woosb_check_qty($this);
  });

  $(document).on('keyup', '.woosb-qty .qty', function() {
    var $this = $(this);

    if (woosb_timeout != null) clearTimeout(woosb_timeout);
    woosb_timeout = setTimeout(woosb_check_qty, 1000, $this);
  });
})(jQuery);

function woosb_init($wrap) {
  var wrap_id = $wrap.attr('data-id');
  var container = woosb_container(wrap_id);
  var $container = $wrap.closest(container);

  woosb_check_ready($container);
  woosb_calc_price($container);
  woosb_save_ids($container);

  jQuery(document).trigger('woosb_init', [$wrap, $container]);
}

function woosb_check_ready($container) {
  var total = 0;
  var selection_name = '';
  var is_selection = false;
  var is_empty = true;
  var is_min = false;
  var is_max = false;
  var $products = $container.find('.woosb-products');
  var $alert = $container.find('.woosb-alert');
  var $btn = $container.find('.single_add_to_cart_button');

  // remove ajax add to cart
  $btn.removeClass('ajax_add_to_cart');

  if (!$products.length ||
      (($products.attr('data-variables') === 'no') &&
          ($products.attr('data-optional') === 'no'))) {
    // don't need to do anything
    return;
  }

  $products.find('.woosb-product').each(function() {
    var $this = jQuery(this);

    if ((
        parseFloat($this.attr('data-qty')) > 0
    ) && (
        parseInt($this.attr('data-id')) === 0
    )) {
      is_selection = true;

      if (selection_name === '') {
        selection_name = $this.attr('data-name');
      }
    }

    if (parseFloat($this.attr('data-qty')) > 0) {
      is_empty = false;
      total += parseFloat($this.attr('data-qty'));
    }
  });

  // check min
  if ((
      $products.attr('data-optional') === 'yes'
  ) && $products.attr('data-min') && (
      total < parseFloat($products.attr('data-min'))
  )) {
    is_min = true;
  }

  // check max
  if ((
      $products.attr('data-optional') === 'yes'
  ) && $products.attr('data-max') && (
      total > parseFloat($products.attr('data-max'))
  )) {
    is_max = true;
  }

  if (is_selection || is_empty || is_min || is_max) {
    $btn.addClass('woosb-disabled');

    if (is_selection) {
      $alert.
          html(woosb_vars.alert_selection.replace('[name]',
              '<strong>' + selection_name + '</strong>')).slideDown();
    } else if (is_empty) {
      $alert.html(woosb_vars.alert_empty).slideDown();
    } else if (is_min) {
      $alert.html(woosb_vars.alert_min.replace('[min]',
          $products.attr('data-min'))).slideDown();
    } else if (is_max) {
      $alert.html(woosb_vars.alert_max.replace('[max]',
          $products.attr('data-max'))).slideDown();
    }

    jQuery(document).
        trigger('woosb_check_ready',
            [false, is_selection, is_empty, is_min, is_max]);
  } else {
    $alert.html('').slideUp();
    $btn.removeClass('woosb-disabled');

    // ready
    jQuery(document).
        trigger('woosb_check_ready',
            [true, is_selection, is_empty, is_min, is_max]);
  }
}

function woosb_calc_price($container) {
  var total = 0;
  var total_sale = 0;
  var $wrap = $container.find('.woosb-wrap');
  var wrap_id = $wrap.attr('data-id');
  var $products = $container.find('.woosb-products');
  var price_suffix = $products.attr('data-price-suffix');
  var $total = $container.find('.woosb-total');
  var $woobt = jQuery('.woobt-wrap-' + wrap_id);
  var total_woobt = parseFloat($woobt.length ? $woobt.attr('data-total') : 0);
  var discount = parseFloat($products.attr('data-discount'));
  var discount_amount = parseFloat($products.attr('data-discount-amount'));
  var fixed_price = $products.attr('data-fixed-price');
  var saved = '';
  var fix = Math.pow(10, Number(woosb_vars.price_decimals) + 1);
  var is_discount = discount > 0 && discount < 100;
  var is_discount_amount = discount_amount > 0;

  $products.find('.woosb-product').each(function() {
    var $this = jQuery(this);
    if (parseFloat($this.attr('data-price')) > 0) {
      var this_price = parseFloat($this.attr('data-price')) *
          parseFloat($this.attr('data-qty'));
      total += this_price;
      if (!is_discount_amount && is_discount) {
        this_price *= (100 - discount) / 100;
        this_price = Math.round(this_price * fix) / fix;
      }
      total_sale += this_price;
    }
  });

  // fix js number https://www.w3schools.com/js/js_numbers.asp
  total = woosb_round(total, woosb_vars.price_decimals);

  if (is_discount_amount && discount_amount < total) {
    total_sale = total - discount_amount;
    saved = woosb_format_price(discount_amount);
  } else if (is_discount) {
    saved = woosb_round(discount, 2) + '%';
  } else {
    total_sale = total;
  }

  if (fixed_price == 'yes') {
    total_sale = parseFloat($products.attr('data-price'));
  }

  var total_html = woosb_price_html(total, total_sale);
  var total_all_html = woosb_price_html(total + total_woobt,
      total_sale + total_woobt);

  if (saved !== '') {
    total_html += ' <small class="woocommerce-price-suffix">' +
        woosb_vars.saved_text.replace('[d]', saved) + '</small>';
  }

  var price_selector = '.summary > .price';

  if ((woosb_vars.change_price === 'yes_custom') &&
      (woosb_vars.price_selector != null) &&
      (woosb_vars.price_selector !== '')) {
    price_selector = woosb_vars.price_selector;
  }

  // change the bundle total
  $total.html(woosb_vars.price_text + ' ' + total_html + price_suffix).
      slideDown();

  if ((
      woosb_vars.change_price !== 'no'
  ) && (
      $products.attr('data-fixed-price') === 'no'
  ) && (
      (
          $products.attr('data-variables') === 'yes'
      ) || (
          $products.attr('data-optional') === 'yes'
      )
  )) {
    // change the main price
    if ($woobt.length) {
      // check if has woobt
      $container.find(price_selector).html(total_all_html + price_suffix);
    } else {
      $container.find(price_selector).html(total_html + price_suffix);
    }
  }

  if ($woobt.length) {
    // check if has woobt
    $woobt.find('.woobt-product-this').attr('data-price', total_sale);
    $woobt.find('.woobt-product-this').attr('data-regular-price', total);

    woobt_init($woobt);
  }

  jQuery(document).
      trigger('woosb_calc_price',
          [total_sale, total, total_html, price_suffix]);
}

function woosb_save_ids($wrap) {
  var ids = Array();
  var $ids = $wrap.find('.woosb-ids');
  var $products = $wrap.find('.woosb-products');

  $products.find('.woosb-product').each(function() {
    var $this = jQuery(this);
    var id = parseInt($this.attr('data-id'));
    var qty = parseFloat($this.attr('data-qty'));
    var attrs = $this.attr('data-attrs');

    if ((id > 0) && (qty > 0)) {
      if (attrs != undefined) {
        attrs = encodeURIComponent(attrs);
      } else {
        attrs = '';
      }

      ids.push(id + '/' + qty + '/' + attrs);
    }
  });

  $ids.val(ids.join(','));

  jQuery(document).trigger('woosb_save_ids', [ids]);
}

function woosb_check_qty($qty) {
  var $wrap = $qty.closest('.woosb-wrap');
  var qty = parseFloat($qty.val());
  var min = parseFloat($qty.attr('min'));
  var max = parseFloat($qty.attr('max'));

  if ((qty === '') || isNaN(qty)) {
    qty = 0;
  }

  if (!isNaN(min) && (
      qty < min
  )) {
    qty = min;
  }

  if (!isNaN(max) && (
      qty > max
  )) {
    qty = max;
  }

  $qty.val(qty);
  $qty.closest('.woosb-product').attr('data-qty', qty);

  // change subtotal
  if (woosb_vars.bundled_price === 'subtotal') {
    var $products = $wrap.find('.woosb-products');
    var $product = $qty.closest('.woosb-product');
    var price_suffix = $product.attr('data-price-suffix');
    var ori_price = parseFloat($product.attr('data-price')) *
        parseFloat($product.attr('data-qty'));

    $product.find('.woosb-price-ori').hide();

    if (parseFloat($products.attr('data-discount')) > 0 &&
        $products.attr('data-fixed-price') === 'no') {
      var new_price = ori_price *
          (100 - parseFloat($products.attr('data-discount'))) / 100;

      $product.find('.woosb-price-new').
          html(woosb_price_html(ori_price, new_price) + price_suffix).show();
    } else {
      $product.find('.woosb-price-new').
          html(woosb_price_html(ori_price) + price_suffix).
          show();
    }
  }

  jQuery(document).trigger('woosb_check_qty', [qty, $qty]);

  woosb_init($wrap);
}

function woosb_change_price($product, price, regular_price, price_html) {
  var $products = $product.closest('.woosb-products');
  var price_suffix = $product.attr('data-price-suffix');

  // hide ori price
  $product.find('.woosb-price-ori').hide();

  // calculate new price
  if (woosb_vars.bundled_price === 'subtotal') {
    var ori_price = parseFloat(price) *
        parseFloat($product.attr('data-qty'));

    if (woosb_vars.bundled_price_from === 'regular_price' &&
        regular_price !== undefined) {
      ori_price = parseFloat(regular_price) *
          parseFloat($product.attr('data-qty'));
    }

    var new_price = ori_price;

    if (parseFloat($products.attr('data-discount')) > 0) {
      new_price = ori_price *
          (100 - parseFloat($products.attr('data-discount'))) / 100;
    }

    $product.find('.woosb-price-new').
        html(woosb_price_html(ori_price, new_price) + price_suffix).show();
  } else {
    if (parseFloat($products.attr('data-discount')) > 0) {
      var ori_price = parseFloat(price);

      if (woosb_vars.bundled_price_from === 'regular_price' &&
          regular_price !== undefined) {
        ori_price = parseFloat(regular_price);
      }

      var new_price = ori_price *
          (100 - parseFloat($products.attr('data-discount'))) / 100;
      $product.find('.woosb-price-new').
          html(woosb_price_html(ori_price, new_price) + price_suffix).show();
    } else {
      if (woosb_vars.bundled_price_from === 'regular_price' &&
          regular_price !== undefined) {
        $product.find('.woosb-price-new').
            html(woosb_price_html(regular_price) + price_suffix).
            show();
      } else if (price_html !== '') {
        $product.find('.woosb-price-new').
            html(price_html).
            show();
      }
    }
  }
}

function woosb_round(value, decimals) {
  return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
}

function woosb_format_money(number, places, symbol, thousand, decimal) {
  number = number || 0;
  places = !isNaN(places = Math.abs(places)) ? places : 2;
  symbol = symbol !== undefined ? symbol : '$';
  thousand = thousand || ',';
  decimal = decimal || '.';

  var negative = number < 0 ? '-' : '',
      i = parseInt(
          number = woosb_round(Math.abs(+number || 0), places).toFixed(places),
          10) + '',
      j = 0;

  if (i.length > 3) {
    j = i.length % 3;
  }

  return symbol + negative + (
      j ? i.substr(0, j) + thousand : ''
  ) + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + thousand) + (
      places ?
          decimal +
          woosb_round(Math.abs(number - i), places).toFixed(places).slice(2) :
          ''
  );
}

function woosb_format_price(price) {
  var price_html = '<span class="woocommerce-Price-amount amount">';
  var price_formatted = woosb_format_money(price, woosb_vars.price_decimals, '',
      woosb_vars.price_thousand_separator, woosb_vars.price_decimal_separator);

  switch (woosb_vars.price_format) {
    case '%1$s%2$s':
      //left
      price_html += '<span class="woocommerce-Price-currencySymbol">' +
          woosb_vars.currency_symbol + '</span>' + price_formatted;
      break;
    case '%1$s %2$s':
      //left with space
      price_html += '<span class="woocommerce-Price-currencySymbol">' +
          woosb_vars.currency_symbol + '</span> ' + price_formatted;
      break;
    case '%2$s%1$s':
      //right
      price_html += price_formatted +
          '<span class="woocommerce-Price-currencySymbol">' +
          woosb_vars.currency_symbol + '</span>';
      break;
    case '%2$s %1$s':
      //right with space
      price_html += price_formatted +
          ' <span class="woocommerce-Price-currencySymbol">' +
          woosb_vars.currency_symbol + '</span>';
      break;
    default:
      //default
      price_html += '<span class="woocommerce-Price-currencySymbol">' +
          woosb_vars.currency_symbol + '</span> ' + price_formatted;
  }

  price_html += '</span>';

  return price_html;
}

function woosb_price_html(regular_price, sale_price) {
  var price_html = '';

  if (sale_price < regular_price) {
    price_html = '<del>' + woosb_format_price(regular_price) + '</del> <ins>' +
        woosb_format_price(sale_price) + '</ins>';
  } else {
    price_html = woosb_format_price(regular_price);
  }

  return price_html;
}

function woosb_decimal_places(num) {
  var match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);

  if (!match) {
    return 0;
  }

  return Math.max(
      0,
      // Number of digits right of decimal point.
      (match[1] ? match[1].length : 0)
      // Adjust for scientific notation.
      - (match[2] ? +match[2] : 0));
}

function woosb_container(id) {
  if ((woosb_vars.container_selector != '') &&
      jQuery(woosb_vars.container_selector).length) {
    return woosb_vars.container_selector;
  }

  if (jQuery('.woosb-wrap-' + id).closest('.woosb-container').length) {
    return '.woosb-container';
  }

  if (jQuery('.woosb-wrap-' + id).closest('#product-' + id).length) {
    return '#product-' + id;
  }

  if (jQuery('.woosb-wrap-' + id).closest('.product.post-' + id).length) {
    return '.product.post-' + id;
  }

  if (jQuery('.woosb-wrap-' + id).closest('div.product-type-woosb').length) {
    return 'div.product-type-woosb';
  }

  if (jQuery('.woosb-wrap-' + id).closest('.elementor-product-woosb').length) {
    return '.elementor-product-woosb';
  }

  return 'body.single-product';
}
