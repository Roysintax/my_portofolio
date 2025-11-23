const container = document.getElementById('canvas-container');

// Scene setup
const scene = new THREE.Scene();
// Add some fog to blend particles into the background
scene.fog = new THREE.FogExp2(0x020c1b, 0.002);

const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });

renderer.setSize(window.innerWidth, window.innerHeight / 2); // Render for the container height (approx)
// But actually, the canvas is resized by CSS. Let's keep renderer full size of container.
renderer.setSize(container.clientWidth, container.clientHeight);
container.appendChild(renderer.domElement);

// Particles
const geometry = new THREE.BufferGeometry();
const particlesCount = 700; // Increased count for density at bottom
const posArray = new Float32Array(particlesCount * 3);
const colorsArray = new Float32Array(particlesCount * 3);

const color1 = new THREE.Color(0x00d2ff); // Cyan
const color2 = new THREE.Color(0x004e92); // Darker Blue
const color3 = new THREE.Color(0x1e90ff); // Dodger Blue

for (let i = 0; i < particlesCount * 3; i += 3) {
    // Spread particles wide (x), but keep them somewhat lower or spread in depth (z)
    posArray[i] = (Math.random() - 0.5) * 15; // x
    posArray[i + 1] = (Math.random() - 0.5) * 10; // y
    posArray[i + 2] = (Math.random() - 0.5) * 15; // z

    // Mix colors
    const mixedColor = Math.random() > 0.5 ? color1 : (Math.random() > 0.5 ? color2 : color3);
    colorsArray[i] = mixedColor.r;
    colorsArray[i + 1] = mixedColor.g;
    colorsArray[i + 2] = mixedColor.b;
}

geometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
geometry.setAttribute('color', new THREE.BufferAttribute(colorsArray, 3));

// Material
const material = new THREE.PointsMaterial({
    size: 0.05,
    vertexColors: true,
    transparent: true,
    opacity: 0.8,
    blending: THREE.AdditiveBlending
});

// Mesh
const particlesMesh = new THREE.Points(geometry, material);
scene.add(particlesMesh);

// Lights (Optional for points but good if we add meshes later)
const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
scene.add(ambientLight);

camera.position.z = 5;
camera.position.y = 0; // Look straight

// Mouse interaction
let mouseX = 0;
let mouseY = 0;

document.addEventListener('mousemove', (event) => {
    mouseX = event.clientX / window.innerWidth - 0.5;
    mouseY = event.clientY / window.innerHeight - 0.5;
});

// Animation Loop
const clock = new THREE.Clock();

function animate() {
    requestAnimationFrame(animate);

    const elapsedTime = clock.getElapsedTime();

    // Rotate entire system slowly
    particlesMesh.rotation.y = elapsedTime * 0.05;
    particlesMesh.rotation.x = mouseY * 0.5;
    particlesMesh.rotation.y += mouseX * 0.5;

    // Wave effect
    for (let i = 0; i < particlesCount; i++) {
        const i3 = i * 3;
        const x = geometry.attributes.position.array[i3];
        // Create a wave motion in Y based on X and Time
        geometry.attributes.position.array[i3 + 1] = Math.sin(elapsedTime + x) * 0.5 + (Math.random() - 0.5) * 0.02;
    }
    geometry.attributes.position.needsUpdate = true;

    renderer.render(scene, camera);
}

animate();

// Resize handler
window.addEventListener('resize', () => {
    camera.aspect = container.clientWidth / container.clientHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(container.clientWidth, container.clientHeight);
});
