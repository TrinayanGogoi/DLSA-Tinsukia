import 'package:flutter/material.dart';
import '../NavigationBar/Sidebar.dart';

class LegalAidPage extends StatelessWidget {
  const LegalAidPage({super.key});

  @override
  Widget build(BuildContext context) {
    return MainLayout(
      child: Center(
        child: Padding(
          padding: const EdgeInsets.only(top: 70.0),
          child: Text(
            'Legal Aid Page',
            style: TextStyle(fontSize: 24),
          ),
        ),
      ),
    );
  }
}