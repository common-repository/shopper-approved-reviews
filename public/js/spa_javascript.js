const ajax_url = spa_javascript_obj.ajaxurl;
const image_path = spa_javascript_obj.image_path;
spa_ajax_main_response(0, "newest");

function spa_ajax_main_response(page, short) {
  const data = {
    action: "spa_load_reviews_ajax",
    page: page,
    short: short
  };
  let html = "";
  let currentRequest = null;
  currentRequest = jQuery.ajax({
    type: "POST",
    data: data,
    url: ajax_url,
    beforeSend: function() {
      if (currentRequest != null) {
        currentRequest.abort();
      }
      jQuery("#loader").show();
      jQuery("#spa_response").empty();
    },
    success: function(response) {
      var reviews = JSON.parse(response);
      jQuery.each(reviews, function(key, review_temp) {
        jQuery.each(review_temp, function(key1, review) {
          html =
            '<div class="spa_review_border whitebg padding"><div class="spa_review_container"><div class="spa_review_title"><span class="spa_reviewer_name marginright10"><a href="' +
            review.fullurl +
            '">' +
            review.name +
            '</a></span><span class="small grey"><span class="pipe">|</span>' +
            review.justlocation +
            "Submitted " +
            review.displaydate +
            '</span></div><div class="spa_star_rating"><img src="' +
            image_path +
            "/colorbox-" +
            parseInt(review.Overall) +
            '.jpg" alt="colorbox-' +
            parseInt(review.Overall) +
            '.jpg"><img src="' +
            image_path +
            "/reviews-" +
            parseInt(review.Overall) +
            '.png" alt="reviews-' +
            parseInt(review.Overall) +
            '.png"></div></div><div class="spa_reviewer_comment"><p class="text-left reviewcomments">' +
            review.textcomments +
            '</p></div><div class="spa_various_ratings"><div class="spa_rating product_satisfaction"><span class="heading">Product Satisfaction</span>' +
            product_rating(review.Product) +
            '</div><div class="spa_rating customer_service"><span class="heading">Customer Service</span>' +
            customer_rating(review.CustomerService) +
            '</div><div class="spa_rating referral"><span class="heading">Referral</span>' +
            referral_rating(review.Referral) +
            "</div></div>";
          jQuery("#spa_response").append(html);
        });
      });
    },
    complete: function() {
      jQuery("#loader").hide();
    },
    error: function() {
      html =
        '<div class="spa_review_border whitebg padding">Some thing went wrong!</div>';
      jQuery("#spa_response").append(html);
    }
  });
}

if (jQuery('div[data-type="spa/spa-render"]').hasClass("is-selected")) {
  spa_ajax_main_response(0, "newest");
}

jQuery(document).on("change", "#review_page", function() {
  const page = jQuery(this).val();
  const short = jQuery("#review_short").val();
  spa_ajax_main_response(page, short);
});

jQuery(document).on("change", "#review_short", function() {
  const short = jQuery(this).val();
  jQuery("#review_page").val("0");
  spa_ajax_main_response(0, short);
});

function product_rating(Product) {
  let product_setisfaction = "";
  for (let i = 1; i <= 5; i++) {
    if (i <= Product) {
      product_setisfaction =
        product_setisfaction + '<span class="fa fa-window-minimize checked"/>';
    } else {
      product_setisfaction =
        product_setisfaction + '<span class="fa fa-window-minimize"/>';
    }
  }
  return product_setisfaction;
}
function customer_rating(CustomerService) {
  let services = "";
  for (let i = 1; i <= 5; i++) {
    if (i <= CustomerService) {
      services = services + '<span class="fa fa-window-minimize checked"/>';
    } else {
      services = services + '<span class="fa fa-window-minimize"/>';
    }
  }
  return services;
}

/**
 * @return {string}
 */
function referral_rating(Referral) {
  let referral_r = "";
  for (let i = 1; i <= 5; i++) {
    if (i <= Referral) {
      referral_r = referral_r + '<span class="fa fa-window-minimize checked"/>';
    } else {
      referral_r = referral_r + '<span class="fa fa-window-minimize"/>';
    }
  }
  return referral_r;
}
