// ===========================
// Throttle helper
// ===========================
// 
// 

// =========================================================
// Add Comments Heading
// =========================================================
document.querySelectorAll('.other-translators').forEach(element => {
    // .trim() removes any accidental spaces or line breaks around the word
    if (element.textContent.trim() === "Translators") {
        element.style.display = 'none';
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".search-form");
    const input = document.getElementById("global-sw");
    const clearBtn = document.getElementById("clear-search-btn");

    if (!form || !input || !clearBtn) return;

    // Show / hide clear button
    input.addEventListener("input", function () {
        clearBtn.style.display = input.value.trim() ? "block" : "none";
    });

    // STOP form submit when clicking clear
    clearBtn.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation(); 

        // Clear input
        input.value = "";
        clearBtn.style.display = "none";
        input.focus();

        // Remove ?s= from URL without reload
        window.history.replaceState({}, document.title, window.location.pathname);
    });

    // Extra safety: prevent accidental submit if input empty
    form.addEventListener("submit", function (e) {
        if (input.value.trim() === "") {
            e.preventDefault();
        }
    });
});

function throttle(fn, limit = 75) {
    let waiting = false;
    return function (...args) {
        if (!waiting) {
            fn.apply(this, args);
            waiting = true;
            setTimeout(() => (waiting = false), limit);
        }
    };
}


function catToggle(btn) {
    var p    = btn.closest('.cat-wrap');
    var more = p.querySelector('.cat-more');
    var dots = p.querySelector('.cat-dots');
    var open = more.classList.contains('open');

    more.classList.toggle('open', !open);
    dots.classList.toggle('hide', !open);

    // Use translated text from data attributes
    btn.textContent = open ? btn.dataset.more : btn.dataset.less;
	btn.classList.toggle('is-open', !open);
btn.classList.toggle('is-closed', open);
}

document.addEventListener("DOMContentLoaded", function () {
// =========================================================
// Related links toggle (robust) + outside click close
// =========================================================
document.addEventListener("click", function (e) {

    const btn = e.target.closest(".toogle-related-btn");

    // =====================================================
    // OUTSIDE CLICK → CLOSE ALL
    // =====================================================
    if (
        !btn &&
        !e.target.closest("article.card") &&
        !e.target.closest(".resource-related-links-content")
    ) {
        document.querySelectorAll(".resource-related-links-content.open").forEach(function (content) {
            content.classList.remove("open");

            const article =
                content.closest("article.card") ||
                content.previousElementSibling ||
                null;

            if (article) {
                const otherBtn = article.querySelector(".toogle-related-btn");
                if (otherBtn) {
                    otherBtn.classList.remove("active");

                    const label = otherBtn.querySelector("span");
                    if (label) label.textContent = wpml_strings.related_links;

                    otherBtn.querySelector("i")?.classList.remove("open");
                }
            }
        });
        return;
    }

    // If click is not on toggle button, stop here
    if (!btn) return;

    // =====================================================
    // HELPERS
    // =====================================================
    function findArticle(node) {
        return node.closest("article.card") || node.closest(".card") || null;
    }

    function findAssociatedContentForArticle(article) {
        if (!article) return null;

        // 1) Inside article
        const inside = article.querySelector(".resource-related-links-content");
        if (inside) return inside;

        // 2) Next siblings
        let el = article.nextElementSibling;
        while (el) {
            if (el.classList?.contains("resource-related-links-content")) return el;
            el = el.nextElementSibling;
        }

        // 3) Previous siblings
        el = article.previousElementSibling;
        while (el) {
            if (el.classList?.contains("resource-related-links-content")) return el;
            el = el.previousElementSibling;
        }

        return null;
    }

    // =====================================================
    // MAIN TOGGLE LOGIC
    // =====================================================
    const wrapper = btn.closest(".resource-related-links-main");
    const article = findArticle(wrapper || btn);
    const content = findAssociatedContentForArticle(article);

    if (!content) {
        btn.classList.toggle("active");
        btn.querySelector("i")?.classList.toggle("open");
        return;
    }

    const isCurrentlyOpen = content.classList.contains("open");

    // Close other open contents
    document.querySelectorAll(".resource-related-links-content.open").forEach(function (otherContent) {
        if (otherContent === content) return;

        otherContent.classList.remove("open");

        const otherArticle =
            otherContent.closest("article.card") ||
            otherContent.previousElementSibling ||
            null;

        if (otherArticle) {
            const otherBtn = otherArticle.querySelector(".toogle-related-btn");
            if (otherBtn) {
                otherBtn.classList.remove("active");

                const otherLabel = otherBtn.querySelector("span");
                if (otherLabel) otherLabel.textContent = wpml_strings.related_links;

                otherBtn.querySelector("i")?.classList.remove("open");
            }
        }
    });

    // Toggle current
    if (isCurrentlyOpen) {
        // CLOSE
        content.classList.remove("open");

        btn.classList.remove("active");
        const label = btn.querySelector("span");
        if (label) label.textContent = wpml_strings.related_links;

        btn.querySelector("i")?.classList.remove("open");
    } else {
        // OPEN
        content.classList.add("open");

        btn.classList.add("active");
        const label = btn.querySelector("span");
        if (label) label.textContent = wpml_strings.close_text;

        btn.querySelector("i")?.classList.add("open");
    }
});



    // =========================================================
    // Header scroll hide / show (THROTTLED)
    // =========================================================
    function initHeaderScroll() {
        const header = document.querySelector('.apum-header');
        if (!header) return;

        let lastScrollTop = window.scrollY;
        let ticking = false;
        const threshold = 128;

        header.style.willChange = 'transform';
        if (!header.style.transition) {
            header.style.transition = 'transform 240ms ease, opacity 240ms ease';
        }

        function updateHeader() {
            const scrollTop = window.scrollY;

            if (scrollTop > threshold) {
                header.classList.toggle('hidden', scrollTop > lastScrollTop);
                header.classList.toggle('scrolled', scrollTop < lastScrollTop);
            } else {
                header.classList.remove('hidden', 'scrolled');
            }

            lastScrollTop = scrollTop;
            ticking = false;
        }

        function onScroll() {
            if (ticking) return;
            ticking = true;
            window.requestAnimationFrame(updateHeader);
        }

        window.addEventListener('scroll', onScroll, { passive: true });
    }

    initHeaderScroll();


    // =========================================================
    // Auto-grow textarea
    // =========================================================
    document.querySelectorAll("textarea").forEach(textarea => {
        const adjustHeight = () => {
            textarea.style.height = "3rem";
            textarea.style.height = textarea.scrollHeight + "px";
        };
        textarea.addEventListener("input", adjustHeight);
        adjustHeight();
    });

    // =========================================================
    // Search popup open / close
    // =========================================================
    const popup = document.getElementById("searchPopup");
    if (popup) {
        const input = popup.querySelector('input[type="search"]');
        const cancelIcon = document.querySelector(".btn-search-cancel");

        document.addEventListener("click", e => {
            if (e.target.closest(".search-popup-open")) {
                popup.classList.add("active");
                setTimeout(() => input?.focus(), 100);
            }
        });

        cancelIcon?.addEventListener("click", () => popup.classList.remove("active"));

        document.addEventListener("keydown", e => {
            if (e.key === "Escape") popup.classList.remove("active");
        });
    }

    // =========================================================
    // Clear Search (multiple)
    // =========================================================
    const searchInputs = document.querySelectorAll(".search-global");
    const clearBtns = document.querySelectorAll(".clear-search-btn");

    if (searchInputs.length === clearBtns.length) {
        searchInputs.forEach((input, i) => {
            const btn = clearBtns[i];

            btn.addEventListener("click", () => {
                input.value = "";
                input.focus();

                const params = new URLSearchParams(window.location.search);
                params.delete("search_query");
                params.delete("paged");

                const base = window.location.pathname;
                window.location.href = params.toString()
                    ? base + "?" + params.toString()
                    : base;
            });

            input.addEventListener("input", () => {
                btn.style.display = input.value.trim() ? "block" : "none";
            });

            input.addEventListener("focus", () => btn.style.display = "block");
            input.addEventListener("blur", () => {
                if (!input.value.trim()) btn.style.display = "none";
            });
        });
    }

    // =========================================================
    // Add Comments Heading
    // =========================================================
    

const commentsArea = document.querySelector(".comments-area");

if (commentsArea) {
    const heading = document.createElement("h3");
    heading.className = "wpd-thread-info maxw-720 mb-3";

    // Detect language
    const lang = document.documentElement.lang;
   
    // Default text
    let text = "Comments and Feedback";

    // Language-based text
    if (lang.startsWith('hi-IN')) {
        text = "टिप्पणियाँ और प्रतिक्रियाएँ";
		
    } else if (lang.startsWith('kn-in')) {
        text = "ನಿಮ್ಮ ಅನಿಸಿಕೆಗಳನ್ನು ಹಂಚಿಕೊಳ್ಳಿ";
    }

    heading.textContent = text;

    commentsArea.prepend(heading);
}
		
		
    // =========================================================
    // Modal accessibility fixes
    // =========================================================
    document.querySelectorAll(".modal").forEach(modal => {
        modal.addEventListener("hide.bs.modal", () => {
            if (modal.classList.contains("show")) {
                const active = document.activeElement;
                if (active && modal.contains(active)) active.blur();
            }
        });
    });

    // =========================================================
    // Tooltip initialization
    // =========================================================
    document.querySelectorAll("[data-bs-toggle='tooltip']").forEach(el => {
        new bootstrap.Tooltip(el);
    });

    // =========================================================
    // Mutation observer for Tagify ARIA label
    // =========================================================
    const observer = new MutationObserver(() => {
        const tagInput = document.querySelector(".tagify__input");
        if (tagInput && !tagInput.hasAttribute("aria-label")) {
            tagInput.setAttribute("aria-label", "Enter keywords");
            observer.disconnect();
        }
    });

    observer.observe(document.body, { childList: true, subtree: true });
});

// =============================================================
// Slick slider (must stay inside jQuery ready)
// =============================================================
jQuery(function ($) {
    $(".common-slick").each(function () {
        const $slider = $(this);

        $slider.on("init afterChange", function (event, slick, currentSlide = 0) {
            const atStart = currentSlide === 0;
            const atEnd = currentSlide >= slick.slideCount - slick.options.slidesToShow;

            $slider.find(".slick-prev").toggle(!atStart);
            $slider.find(".slick-next").toggle(!atEnd);
        });

        $slider.slick({
            slidesToShow: 3,
            slidesToScroll: 1,
            infinite: false,
            arrows: true,
            dots: false,
            prevArrow: '<button type="button" class="slick-prev slick-arrow" aria-label="Previous slide"><i class="fa-solid fa-arrow-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next slick-arrow" aria-label="Next slide"><i class="fa-solid fa-arrow-right"></i></button>',
            responsive: [
                { breakpoint: 992, settings: { slidesToShow: 2 } },
                { breakpoint: 768, settings: { slidesToShow: 1 } }
            ]
        });
    });
});


jQuery(document).ready(function($) {

	function enableSubmenuToggle() {
		// Hide all submenus and remove open classes
		$('header .menu-item-has-children .sub-menu, header .web-langbar .sub-menu, header .lang-menu .sub-menu').hide();
		$('header .menu-item-has-children, header .web-langbar, header .lang-menu').removeClass('open');

		// Remove previous handlers
		$('header .menu-item-has-children > a').off('click').on('click', function(e) {
			e.preventDefault();

			var $submenu = $(this).next('.sub-menu');
			var $parent = $(this).parent();

			// Only toggle .menu-item-has-children
			$('header .menu-item-has-children .sub-menu').not($submenu).slideUp();
			$('header .menu-item-has-children').not($parent).removeClass('open');

			if ($parent.hasClass('open')) {
    $submenu.stop(true, true).slideUp();
    $parent.removeClass('open');
} else {
    $submenu.stop(true, true).slideDown();
    $parent.addClass('open');
};

			e.stopPropagation();
		});

		$('header .web-langbar > a').off('click').on('click', function(e) {
			e.preventDefault();

			var $submenu = $(this).next('.sub-menu');
			var $parent = $(this).parent();

			// Only toggle .web-langbar
			$('header .web-langbar .sub-menu').not($submenu).slideUp();
			$('header .web-langbar').not($parent).removeClass('open');

			$submenu.stop(true, true).slideToggle();
			$parent.toggleClass('open');

			e.stopPropagation();
		});

		$('header .lang-menu > a').off('click').on('click', function(e) {
			e.preventDefault();

			var $submenu = $(this).next('.sub-menu');
			var $parent = $(this).parent();

			// Only toggle .lang-menu
			$('header .lang-menu .sub-menu').not($submenu).slideUp();
			$('header .lang-menu').not($parent).removeClass('open');

			$submenu.stop(true, true).slideToggle();
			$parent.toggleClass('open');

			e.stopPropagation();
		});
	}

	function resetSubmenuOnLargeScreen() {
		$('header .menu-item-has-children .sub-menu, header .web-langbar .sub-menu, header .lang-menu .sub-menu').removeAttr('style');
		$('header .menu-item-has-children > a, header .web-langbar > a, header .lang-menu > a').off('click');
		$('header .menu-item-has-children, header .web-langbar, header .lang-menu').removeClass('open');
	}

	function checkScreenWidth() {
		if (window.innerWidth < 1200) {
			enableSubmenuToggle();
		} else {
			resetSubmenuOnLargeScreen();
		}
	}

	checkScreenWidth();
	$(window).resize(function() {
		checkScreenWidth();
	});
});

// mobile dropdown functionality code end here
(function () {

    const htmlLang = (document.documentElement.lang || "").toLowerCase();

    // Alphabets per language
    const alphabets = {
        en: "abcdefghijklmnopqrstuvwxyz".split(""),
           hi: [
   "क","ख","ग","घ","ङ","च","छ","ज","झ","ञ",
    "ट","ठ","ड","ढ","ण","त","थ","द","ध","न",
     "प","फ","ब","भ","म","य","र","ल","व",
      "श","ष","स","ह"
],
        kn: [
            "ಅ","ಆ","ಇ","ಈ","ಉ","ಊ","ಋ","ಎ","ಏ","ಐ",
            "ಒ","ಓ","ಔ","ಕ","ಖ","ಗ","ಘ","ಙ","ಚ","ಛ",
            "ಜ","ಝ","ಞ","ಟ","ಠ","ಡ","ಢ","ಣ","ತ","ಥ",
            "ದ","ಧ","ನ","ಪ","ಫ","ಬ","ಭ","ಮ","ಯ",
            "ರ","ಲ","ವ","ಶ","ಷ","ಸ","ಹ"
        ]
    };

    // Digits per language
    const digitsMap = {
        en: ['0','1','2','3','4','5','6','7','8','9'],
        hi: ['०','१','२','३','४','५','६','७','८','९'],
        kn: ['೦','೧','೨','೩','೪','೫','೬','೭','೮','೯']
    };

    // Start mapping for Hindi/ Kannada
    const startMap = {
        hi: { a:0,b:1,c:2,d:3,e:4,f:5,g:6,h:7,i:8,j:9,k:10,l:11,m:12,n:13,o:14,p:15,q:16,r:17,s:18,t:19,u:20,v:21,w:22,x:23,y:24,z:25 },
        kn: { a:0,b:1,c:2,d:3,e:4,f:5,g:6,h:7,i:8,j:9,k:10,l:11,m:12,n:13,o:14,p:15,q:16,r:17,s:18,t:19,u:20,v:21,w:22,x:23,y:24,z:25 }
    };

    // Detect language
    let langKey = 'en';
    if (htmlLang.startsWith("hi")) langKey = 'hi';
    if (htmlLang.startsWith("kn")) langKey = 'kn';

    const alphaSet = alphabets[langKey];
    const digits = digitsMap[langKey];

    /* ---------------------------
       1️⃣ Alphabet lists
    --------------------------- */
    document.querySelectorAll(".list-type-alpha").forEach(list => {
        let startIndex = 0;

        // Determine start letter from class: start-a, start-b, etc.
        list.classList.forEach(cls => {
            const match = cls.match(/^start-([a-z])$/i);
            if (match) {
                const ch = match[1].toLowerCase();
                startIndex = langKey === 'en'
                    ? ch.charCodeAt(0) - 97
                    : (startMap[langKey][ch] || 0);
            }
        });

        const isUpper = list.classList.contains("is-uppercase");

        list.querySelectorAll("li").forEach((li,i)=>{
            const pos = startIndex + i;
            let letter = alphaSet[pos] || alphaSet[alphaSet.length-1];
            if(langKey === 'en' && isUpper) letter = letter.toUpperCase();
            li.setAttribute("data-alpha", letter);
        });
    });

    /* ---------------------------
       2️⃣ Numeric lists (list-type-counter)
    --------------------------- */
  document.querySelectorAll('.list-type-counter').forEach(ul => {

        // Determine start from class (start-1 ... start-10)
        let start = 1;
        ul.classList.forEach(cls => {
            const match = cls.match(/^start-(\d+)$/);
            if(match) start = parseInt(match[1],10);
        });

        ul.querySelectorAll('li').forEach((li, i) => {
            const num = start + i;
            if(num > 10) return; // optional max limit

            const localized = num.toString().split('').map(d => digits[+d]).join('');
            const span = document.createElement("span");
            span.className = "list-num";
            span.textContent = localized + ") ";
            li.prepend(span);
        });
    });

})();

