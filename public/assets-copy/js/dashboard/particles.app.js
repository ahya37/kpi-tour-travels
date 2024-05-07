/* -----------------------------------------------
/* How to use? : Check the GitHub README
/* ----------------------------------------------- */

/* To load a config file (particles.json) you need to host this demo (MAMP/WAMP/local)... */
/*
particlesJS.load('particles-js', 'particles.json', function() {
  console.log('particles.js loaded - callback');
});
*/

/* Otherwise just put the config content (json): */
! function(e) {
   e("#particles-snow").length > 0 && particlesJS("particles-snow", {
       
		  "particles": {
			"number": {
			  "value": 80,
			  "density": {
				"enable": true,
				"value_area": 800
			  }
			},
			"color": {
			  "value": "#1e33f2"
			},
			"shape": {
			  "type": "circle",
			  "stroke": {
				"width": 0,
				"color": "#1e33f2"
			  },
			  "polygon": {
				"nb_sides": 5
			  },
			  "image": {
				"src": "img/github.svg",
				"width": 100,
				"height": 100
			  }
			},
			"opacity": {
			  "value": 0.5,
			  "random": false,
			  "anim": {
				"enable": false,
				"speed": 1,
				"opacity_min": 0.1,
				"sync": false
			  }
			},
			"size": {
			  "value": 3,
			  "random": true,
			  "anim": {
				"enable": false,
				"speed": 50,
				"size_min": 0.1,
				"sync": false
			  }
			},
			"line_linked": {
			  "enable": true,
			  "distance": 100,
			  "color": "#1e33f2",
			  "opacity": 0.4,
			  "width": 1
			},
			
		  },
		  "interactivity": {
			"detect_on": "canvas",
			"events": {
			  "onhover": {
				"enable": false,
				"mode": "repulse"
			  },
			  
			  
			},
			"modes": {
			  "grab": {
				"distance": 400,
				"line_linked": {
				  "opacity": 1
				}
			  },
			  "bubble": {
				"distance": 400,
				"size": 10,
				"duration": 2,
				"opacity": 0.8,
				"speed": 3
			  },
			  
			  
			  
			}
		  },
		  "retina_detect": true
		
    })
	e("#particles-snow1").length > 0 && particlesJS("particles-snow1", {
        particles: {
            number: {
                value: 25,
                density: {
                    enable: !0,
                    value_area: 800
                }
            },
            color: {
                value: "#ffffff"
            },
            shape: {
                type: "image",
                stroke: {
                    width: 8,
                    color: "#fff"
                },
                polygon: {
                    nb_sides: 10
                },
                image: {
                    src: "welcome/images/snow.png",
                    width: 100,
                    height: 100
                }
            },
            opacity: {
                value: .7,
                random: !1,
                anim: {
                    enable: !1,
                    speed: 2,
                    opacity_min: .1,
                    sync: !1
                }
            },
            size: {
                value: 10,
                random: !0,
                anim: {
                    enable: !1,
                    speed: 10,
                    size_min: .1,
                    sync: !1
                }
            },
            line_linked: {
                enable: !1,
                distance: 50,
                color: "#ffffff",
                opacity: .6,
                width: 1
            },
            move: {
                enable: !0,
                speed: 1,
                direction: "top",
                random: !0,
                straight: !1,
                out_mode: "out",
                bounce: !1,
                attract: {
                    enable: !0,
                    rotateX: 300,
                    rotateY: 1200
                }
            }
        },
        interactivity: {
            detect_on: "canvas",
            events: {
                onhover: {
                    enable: !0,
                    mode: "bubble"
                },
                onclick: {
                    enable: !0,
                    mode: "repulse"
                },
                resize: !0
            },
            modes: {
                grab: {
                    distance: 150,
                    line_linked: {
                        opacity: 1
                    }
                },
                bubble: {
                    distance: 200,
                    size: 10,
                    duration: 2,
                    opacity: 8,
                    speed: 3
                },
                repulse: {
                    distance: 200,
                    duration: .2
                },
                push: {
                    particles_nb: 4
                },
                remove: {
                    particles_nb: 2
                }
            }
        },
        retina_detect: !0
    })
}(jQuery);