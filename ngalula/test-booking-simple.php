<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Booking Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .therapist-card {
            background: white;
            border: 2px solid #6366f1;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            cursor: pointer;
        }
        h3 {
            color: #1f2937;
            margin-bottom: 10px;
        }
        p {
            color: #6b7280;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <h1>Simple Booking Test</h1>
    <p>Testing therapist display without Laravel Blade</p>
    
    <div class="therapist-card">
        <h3>Sarah Johnson</h3>
        <p>Specialty: Massage Therapy</p>
        <p>Experience: 8 years</p>
        <p>Rating: 5.0★</p>
        <p>Bio: Professional therapist with 8+ years experience</p>
    </div>
    
    <div class="therapist-card">
        <h3>Michael Chen</h3>
        <p>Specialty: Wellness Expert</p>
        <p>Experience: 12 years</p>
        <p>Rating: 4.8★</p>
        <p>Bio: Holistic wellness practitioner</p>
    </div>
    
    <div class="therapist-card">
        <h3>Emily Davis</h3>
        <p>Specialty: Facial Specialist</p>
        <p>Experience: 6 years</p>
        <p>Rating: 5.0★</p>
        <p>Bio: Licensed esthetician</p>
    </div>
    
    <script>
        console.log('Simple booking test loaded');
        document.querySelectorAll('.therapist-card').forEach(card => {
            card.addEventListener('click', function() {
                alert('Therapist clicked: ' + card.querySelector('h3').textContent);
            });
        });
    </script>
</body>
</html>
