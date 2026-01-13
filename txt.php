<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IA Dashboard | Data Analysis</title>
    <style>
        /* ----- GLOBAL ----- */
        body {
            margin: 0;
            font-family: "Poppins", sans-serif;
            background: #0a0a0f;
            color: white;
        }

        a { text-decoration: none; color: inherit; }

        /* ----- HEADER ----- */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            background: rgba(255, 255, 255, 0.03);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        header h1 {
            font-size: 1.6rem;
            color: #5ddcff;
            letter-spacing: 1px;
        }

        nav a {
            margin: 0 15px;
            font-size: 1rem;
            opacity: 0.8;
            transition: 0.3s;
        }

        nav a:hover { opacity: 1; color: #5ddcff; }

        /* ----- HERO ----- */
        .hero {
            text-align: center;
            padding: 120px 20px;
            background: linear-gradient(135deg, #0a0a0f, #111120);
        }

        .hero h2 {
            font-size: 3rem;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #5ddcff, #a558ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 1.2rem;
            opacity: 0.8;
            margin-bottom: 30px;
        }

        .hero button {
            background: #5ddcff;
            border: none;
            padding: 15px 30px;
            font-size: 1rem;
            cursor: pointer;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
            color: #000;
        }

        .hero button:hover {
            transform: scale(1.05);
            background: #8be0ff;
        }

        /* ----- SECTIONS ----- */
        section {
            padding: 80px 40px;
        }

        .section-title {
            text-align: center;
            font-size: 2.2rem;
            margin-bottom: 60px;
        }

        /* ----- SERVICES ----- */
        .cards {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
        }

        .card {
            background: #14141e;
            padding: 30px;
            border-radius: 12px;
            width: 300px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-10px);
            border-color: #5ddcff;
        }

        .card h3 {
            color: #5ddcff;
            margin-bottom: 10px;
        }

        .card p {
            opacity: 0.8;
        }

       