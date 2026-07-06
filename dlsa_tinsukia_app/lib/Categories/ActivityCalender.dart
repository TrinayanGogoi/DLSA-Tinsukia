import 'package:flutter/material.dart';
import '../NavigationBar/Sidebar.dart'; // Adjust the path if needed

class ActivityCalender extends StatelessWidget {
  const ActivityCalender({super.key});

  @override
  Widget build(BuildContext context) {
    return MainLayout(
      child: Center(
        child: Text(
          'Activity Calender Page !! Do at Last',
          style: TextStyle(fontSize: 24),
        ),
      ),
    );
  }
}