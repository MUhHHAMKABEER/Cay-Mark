@extends('layouts.welcome')

@section('content')

<style>
    .contact-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #1e40af 100%);
        position: relative;
        overflow: hidden;
    }
    
    .contact-hero::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 50%;
        height: 100%;
        background-image: url('{{ asset('images/contact-bg.jpg') }}');
        background-size: cover;
        background-position: center;
        opacity: 0.2;
        filter: blur(20px);
    }
    
    .floating-shape {
        position: absolute;
        border-radius: 50%;
        opacity: 0.1;
        animation: float 20s infinite ease-in-out;
    }
    
    @keyframes float {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        25% { transform: translate(30px, -30px) rotate(90deg); }
        50% { transform: translate(-20px, 20px) rotate(180deg); }
        75% { transform: translate(20px, 30px) rotate(270deg); }
    }
    
    .contact-card {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        margin-top: -80px;
        position: relative;
        z-index: 10;
    }
    
    .contact-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }
    
    .form-input {
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 0.875rem 1rem;
        transition: all 0.3s ease;
        background: #f9fafb;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        background: white;
    }
    
    .social-icon {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2);
    }
    
    .social-icon:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
    }
    
    .submit-btn {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }
    
    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
    }
    
    .map-container-full {
        width: 100%;
        height: 600px;
        overflow: hidden;
        position: relative;
    }
    
    #threejs-canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        opacity: 0.3;
    }
</style>

<!-- Hero Section with Dark Blue Gradient -->
<section class="contact-hero py-32 px-4 relative">
    <!-- Floating decorative shapes -->
    <div class="floating-shape" style="width: 200px; height: 200px; background: rgba(255, 255, 255, 0.1); top: 10%; left: 5%; animation-delay: 0s;"></div>
    <div class="floating-shape" style="width: 150px; height: 150px; background: rgba(255, 255, 255, 0.1); top: 60%; right: 10%; animation-delay: 2s;"></div>
    <div class="floating-shape" style="width: 100px; height: 100px; background: rgba(255, 255, 255, 0.1); bottom: 20%; left: 15%; animation-delay: 4s;"></div>
    
    <div class="container mx-auto max-w-7xl relative z-10">
        <div class="text-center text-white">
            <div class="inline-flex items-center bg-white/10 backdrop-blur-md rounded-full px-5 py-2.5 mb-6 border border-white/20">
                <span class="w-2.5 h-2.5 bg-green-400 rounded-full mr-3 animate-pulse"></span>
                <span class="text-sm font-semibold">We're here to help!</span>
            </div>
            <h1 class="text-5xl md:text-7xl font-extrabold mb-6 font-heading drop-shadow-2xl">Contact us</h1>
            <p class="text-xl md:text-2xl text-blue-100 max-w-3xl mx-auto leading-relaxed">
                CayMark is ready to provide the right solution according to your needs.
            </p>
        </div>
    </div>
</section>

<!-- Main Contact Card -->
<section class="py-16 px-4">
    <div class="container mx-auto max-w-7xl">
        <div class="contact-card p-10 md:p-16">
            <div class="grid md:grid-cols-2 gap-16">
                <!-- Left Column - Get in touch -->
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Get in touch</h2>
                    <p class="text-gray-600 mb-10 leading-relaxed text-lg">
                        We're here to help! Reach out to us for any questions, support, or inquiries about our vehicle auction platform. Our team is committed to providing you with the best service experience.
                    </p>
                    
                    <!-- Contact Details -->
                    <div class="space-y-8 mb-10">
                        <!-- Email Us -->
                        <div class="flex items-start">
                            <div class="contact-icon mr-4 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 mb-1">Email us</h3>
                                <p class="text-gray-600">
                                    General inquiries: <a href="mailto:info@caymark.co" class="hover:text-blue-600 transition-colors">info@caymark.co</a><br>
                                    Customer support: <a href="mailto:support@caymark.co" class="hover:text-blue-600 transition-colors">support@caymark.co</a>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Call Us -->
                        <div class="flex items-start">
                            <div class="contact-icon mr-4 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 mb-1">Call Us</h3>
                                <p class="text-gray-600">
                                    Phone: <a href="tel:+12421234567" class="hover:text-blue-600 transition-colors">+1 (242) 123-4567</a><br>
                                    Fax: +1 (242) 123-4568
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Business Hours -->
                    <div class="info-card mb-8">
                        <div class="flex items-center mb-4">
                            <div class="contact-icon mr-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-900 text-xl">Business Hours</h3>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-gray-700 font-medium">Monday - Friday</span>
                                <span class="text-gray-900 font-bold">9:00 AM - 6:00 PM</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-gray-700 font-medium">Saturday</span>
                                <span class="text-gray-900 font-bold">10:00 AM - 4:00 PM</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-700 font-medium">Sunday</span>
                                <span class="text-gray-500 font-bold">Closed</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Social Media (Facebook, Instagram, TikTok only) -->
                    <div>
                        <h3 class="font-bold text-gray-900 mb-4">Follow us</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="social-icon" title="Facebook">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                            <a href="#" class="social-icon" title="Instagram">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </a>
                            <a href="#" class="social-icon" title="TikTok">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column - Send us a message -->
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Send us a message</h2>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        Fill out the form below and we'll get back to you as soon as possible. We typically respond within 24 hours.
                    </p>
                    <form action="#" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                                <input type="text" id="name" name="name" required class="w-full form-input" placeholder="Enter your name">
                            </div>
                            <div>
                                <label for="company" class="block text-sm font-semibold text-gray-700 mb-2">Company</label>
                                <input type="text" id="company" name="company" class="w-full form-input" placeholder="Enter your company">
                            </div>
                        </div>
                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                                <input type="tel" id="phone" name="phone" required class="w-full form-input" placeholder="Enter your phone">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                                <input type="email" id="email" name="email" required class="w-full form-input" placeholder="Enter your email">
                            </div>
                        </div>
                        <div>
                            <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">Subject</label>
                            <input type="text" id="subject" name="subject" required class="w-full form-input" placeholder="Enter subject">
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">Message</label>
                            <textarea id="message" name="message" rows="6" required class="w-full form-input resize-none" placeholder="Enter your message"></textarea>
                        </div>
                        <button type="submit" class="w-full submit-btn text-white font-bold py-4 px-6 rounded-xl">
                            Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Google Maps Section - Full Width -->
<section class="py-0 relative overflow-hidden">
    <!-- Three.js Background Canvas -->
    <canvas id="threejs-canvas" class="absolute inset-0 z-0"></canvas>
    
    <div class="relative z-10">
        <div class="text-center py-12 px-4 bg-white/95 backdrop-blur-sm">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">Find Us</h2>
            <p class="text-gray-600">Visit our office in Nassau, The Bahamas</p>
        </div>
        <div class="map-container-full relative z-10">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3610.178509579!2d-77.343055684951!3d25.077196983899!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x892f7c99b981dbc9%3A0x2d0c2b5c8b8b8b8b!2sBay%20Street%2C%20Nassau%2C%20The%20Bahamas!5e0!3m2!1sen!2sus!4v1234567890123!5m2!1sen!2sus"
                width="100%" 
                height="100%" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

<!-- Three.js Script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('threejs-canvas');
    if (!canvas || typeof THREE === 'undefined') return;
    
    const section = canvas.closest('section');
    if (!section) return;
    
    // Get section dimensions
    const getDimensions = () => {
        const rect = section.getBoundingClientRect();
        return { width: rect.width, height: rect.height };
    };
    
    let { width, height } = getDimensions();
    
    // Initialize Three.js Scene
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, width / height, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ 
        canvas: canvas, 
        alpha: true,
        antialias: true 
    });
    
    renderer.setSize(width, height);
    renderer.setPixelRatio(window.devicePixelRatio);
    
    // Create floating particles
    const particlesGeometry = new THREE.BufferGeometry();
    const particlesCount = 800;
    const posArray = new Float32Array(particlesCount * 3);
    
    for (let i = 0; i < particlesCount * 3; i++) {
        posArray[i] = (Math.random() - 0.5) * 25;
    }
    
    particlesGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
    
    const particlesMaterial = new THREE.PointsMaterial({
        size: 0.08,
        color: 0x3b82f6,
        transparent: true,
        opacity: 0.4,
        blending: THREE.AdditiveBlending
    });
    
    const particlesMesh = new THREE.Points(particlesGeometry, particlesMaterial);
    scene.add(particlesMesh);
    
    // Create animated geometric shapes
    const geometry = new THREE.TorusGeometry(3, 0.6, 16, 100);
    const material = new THREE.MeshStandardMaterial({ 
        color: 0x2563eb,
        transparent: true,
        opacity: 0.2,
        wireframe: true
    });
    const torus = new THREE.Mesh(geometry, material);
    scene.add(torus);
    
    // Add another shape
    const sphereGeometry = new THREE.SphereGeometry(2, 32, 32);
    const sphereMaterial = new THREE.MeshStandardMaterial({ 
        color: 0x1e40af,
        transparent: true,
        opacity: 0.15,
        wireframe: true
    });
    const sphere = new THREE.Mesh(sphereGeometry, sphereMaterial);
    sphere.position.set(0, 0, -5);
    scene.add(sphere);
    
    // Add ambient light
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.4);
    scene.add(ambientLight);
    
    // Add directional light
    const directionalLight = new THREE.DirectionalLight(0x3b82f6, 0.6);
    directionalLight.position.set(5, 5, 5);
    scene.add(directionalLight);
    
    camera.position.z = 8;
    
    // Animation loop
    function animate() {
        requestAnimationFrame(animate);
        
        // Rotate shapes
        torus.rotation.x += 0.008;
        torus.rotation.y += 0.01;
        sphere.rotation.x += 0.005;
        sphere.rotation.y += 0.008;
        
        // Animate particles
        particlesMesh.rotation.y += 0.001;
        particlesMesh.rotation.x += 0.0005;
        
        renderer.render(scene, camera);
    }
    
    // Handle window resize
    function handleResize() {
        const dims = getDimensions();
        width = dims.width;
        height = dims.height;
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        renderer.setSize(width, height);
    }
    
    window.addEventListener('resize', handleResize);
    animate();
});
</script>

@endsection
