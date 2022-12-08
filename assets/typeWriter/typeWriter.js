var slogan = document.getElementById('slogan');

new Typewriter(slogan, {
    loop: true,
    deleteSpeed: 20
})
.pauseFor(500)
.typeString('Pièces détachées <span class="text-danger">ET</span> jeux complets')
.start()
.pauseFor(2000)