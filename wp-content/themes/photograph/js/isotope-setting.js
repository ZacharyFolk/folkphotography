jQuery(function(){var e=jQuery(".featured-gallery, .post-featured-gallery");jQuery(window).on("load",function(){e.imagesLoaded(function(){e.isotope({itemSelector:".featured-item, .post-featured-item"})}),jQuery(".filter-button").on("click","button",function(){var t=jQuery(this).attr("data-category");e.isotope({filter:t}),jQuery(".filter-button button").removeClass("active"),jQuery(this).addClass("active")})})});