<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;

class AboutController extends Controller
{
    public function index()
    {
        $admin = User::where('is_admin', true)->first();

        $skills = [
            'Frontend'  => ['React', 'Next.js', 'Vue.js', 'HTML5', 'CSS3', 'Tailwind CSS', 'JavaScript', 'TypeScript'],
            'Backend'   => ['PHP', 'Laravel', 'Node.js', 'Express.js', 'REST APIs', 'GraphQL'],
            'Mobile'    => ['React Native', 'Electron'],
            'Database'  => ['MySQL', 'PostgreSQL', 'MongoDB', 'Redis'],
            'Tools'     => ['Git', 'Docker', 'Linux', 'AWS', 'Figma', 'Vite'],
        ];

        $timeline = [
            ['year' => '2024', 'title' => 'Senior Full Stack Developer', 'place' => 'Freelance / Remote',        'desc' => 'Building scalable web applications for clients worldwide using Laravel, React, and modern cloud infrastructure.'],
            ['year' => '2022', 'title' => 'Full Stack Developer',        'place' => 'Tech Startup',               'desc' => 'Led frontend architecture migration from jQuery to React, improving performance by 60%.'],
            ['year' => '2020', 'title' => 'Junior Web Developer',        'place' => 'Digital Agency',             'desc' => 'Developed and maintained client websites using PHP/Laravel and vanilla JavaScript.'],
            ['year' => '2019', 'title' => 'B.Sc. Computer Science',      'place' => 'University of Technology',   'desc' => 'Graduated with honours. Thesis on real-time collaborative systems using WebSockets.'],
        ];

        return view('frontend.about', compact('admin', 'skills', 'timeline'));
    }
}
