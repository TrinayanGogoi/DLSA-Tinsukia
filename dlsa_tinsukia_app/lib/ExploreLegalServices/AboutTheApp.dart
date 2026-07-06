import 'package:flutter/material.dart';
import '../NavigationBar/Sidebar.dart';

class AboutTheAppPage extends StatelessWidget {
  const AboutTheAppPage({super.key});

  @override
  Widget build(BuildContext context) {
    return MainLayout(
      child: Center(
        child: Padding(
          padding: const EdgeInsets.only(top: 70.0),
          child: Text(
            'About The App Page',
            style: TextStyle(fontSize: 24),
          ),
        ),
      ),
    );
  }
}