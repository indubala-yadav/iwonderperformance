// document.addEventListener('DOMContentLoaded', function () {
// 	if (
// 		typeof gsap !== "undefined" &&
// 		typeof ScrollTrigger !== "undefined" &&
// 		document.querySelector(".apum-animation")
// 	) {
// 		gsap.registerPlugin(ScrollTrigger);

// 		// Animate elements already in viewport on load
// 		document.querySelectorAll('.apum-animation').forEach(el => {
// 			const rect = el.getBoundingClientRect();
// 			if (rect.top < window.innerHeight) {
// 				gsap.from(el, {
// 					y: 50,
// 					opacity: 0,
// 					duration: 1,
// 					ease: "power3.out"
// 				});
// 			}
// 		});

// 		// Animate remaining elements on scroll
// 		gsap.from(".apum-animation", {
// 			scrollTrigger: {
// 				trigger: ".apum-animation-fadeInUp",
// 				start: "top 80%",
// 				toggleActions: "play none none none",
// 				markers: false
// 			},
// 			y: 50,
// 			opacity: 0,
// 			stagger: 0.2,
// 			duration: 1,
// 			ease: "power3.out"
// 		});
// 	}
// });


// document.addEventListener('DOMContentLoaded', function () {
//   const sections = document.querySelectorAll(".apum-animation-fadeInUp");

//   function isVisible(el) {
//     return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length);
//   }

//   function revealOnScroll() {
//     const windowHeight = window.innerHeight;

//     sections.forEach(function (section) {
//       const rect = section.getBoundingClientRect();

//       // Skip hidden/popup elements
//       if (!isVisible(section)) return;

//       if (rect.top < windowHeight - 50) {
//         section.classList.add("visible");
//       }
//     });
//   }


//   // Safari/Brave fix — short timeout helps with paint/render
//   setTimeout(() => {
//     revealOnScroll(); // show section on page load
//     window.addEventListener("scroll", revealOnScroll);
//   }, 10);
// });





document.addEventListener('DOMContentLoaded', function () {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

    gsap.registerPlugin(ScrollTrigger);

    // Animate only visible sections on scroll
    const fadeInElems = document.querySelectorAll(".apum-animation-fadeInUp");

    fadeInElems.forEach((elem) => {
        // Skip hidden ones (popups/modals/etc)
        const isVisible = !!(elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length);
        if (!isVisible) return;

        gsap.from(elem, {
            scrollTrigger: {
                trigger: elem,
                start: "top 80%",
                toggleActions: "play none none none",
                once: true, // only play once
                markers: false
            },
            y: 50,
            opacity: 0,
            duration: 1,
            ease: "power3.out"
        });
    });
});