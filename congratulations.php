<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Congratulations</title>
  <style>
    body {
      margin: 0;
      overflow: hidden;
      background: #1e1e2f;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      color: white;
      font-family: Arial, sans-serif;
    }

    .message {
      font-size: 3rem;
      font-weight: bold;
      position: absolute;
      text-shadow: 0 0 10px #fff;
      opacity: 0;
      animation: fadeIn 2s ease-out forwards;
    }

    @keyframes fadeIn {
      0% {
        opacity: 0;
        transform: scale(0.8);
      }
      100% {
        opacity: 1;
        transform: scale(1);
      }
    }

    .particle {
      position: absolute;
      width: 10px;
      height: 10px;
      background: radial-gradient(circle, #57a6e6, transparent);
      border-radius: 50%;
      animation: explode 1s ease-out forwards;
    }

    .particle1 {
      position: absolute;
      width: 10px;
      height: 10px;
      background: radial-gradient(circle, #ff0000, transparent);
      border-radius: 50%;
      animation: explode 1s ease-out forwards;
    }

    @keyframes explode {
      0% {
        opacity: 1;
        transform: translate(0, 0) scale(1);
      }
      100% {
        opacity: 0;
        transform: translate(calc(var(--x) * 1px), calc(var(--y) * 1px)) scale(0.5);
      }
    }
  </style>
</head>
<body>
  <div class="message">
    <center>Congratulations!<br>Your order has been picked by: <span id="acceptorEmail"></span></center>
  </div>
  <script>
    const particleCount = 100;
    for (let i = 0; i < particleCount; i++) {
      const particle = document.createElement('div');
      if(i % 2 === 0) {
        particle.className = 'particle';
      } else {
        particle.className = 'particle1';
      }
      const angle = Math.random() * 2 * Math.PI;
      const distance = Math.random() * 300;
      particle.style.setProperty('--x', Math.cos(angle) * distance);
      particle.style.setProperty('--y', Math.sin(angle) * distance);
      particle.style.left = `${window.innerWidth / 2}px`;
      particle.style.top = `${window.innerHeight / 2}px`;
      document.body.appendChild(particle);
      particle.addEventListener('animationend', () => particle.remove());
    }

    const params = new URLSearchParams(window.location.search);
    document.getElementById('acceptorEmail').textContent = params.get('acceptorEmail');
  </script>
</body>
</html>
