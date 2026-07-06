import 'package:flutter/material.dart';
import 'package:flutter_native_splash/flutter_native_splash.dart';
import 'NavigationBar/Sidebar.dart';
import 'Widgets/ImageSlider.dart';
import 'Widgets/LatestSection.dart';
import 'ExploreLegalServices/AboutTheApp.dart';
import 'ExploreLegalServices/Legal_Aid.dart';

void main() async{
  WidgetsBinding widgetsBinding = WidgetsFlutterBinding.ensureInitialized();
  FlutterNativeSplash.preserve(widgetsBinding: widgetsBinding);
  await Future.delayed(
    Duration(seconds: 0),
    );
    // whenever your initialization is completed, remove the splash screen:
    FlutterNativeSplash.remove();
  runApp(MyApp());
}


class MyApp extends StatelessWidget {
  MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      home: HomeScreen(),
    );
  }
}

class HomeScreen extends StatelessWidget {
  HomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return MainLayout(
      child: Column(
        children: [

          Padding(
            padding: EdgeInsets.only(top: 50.0,),
            child: ImageSlider(),
          ),
          
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.center,
              children: [
                const Text(
                  'Explore Legal Services',
                  style: TextStyle(
                    fontSize: 24,
                    fontWeight: FontWeight.bold,
                    color: Color.fromARGB(255, 122, 120, 120),
                  ),
                ),
                const SizedBox(height: 10),
                GridView.count(
                  shrinkWrap: true,
                  physics: NeverScrollableScrollPhysics(),
                  crossAxisCount: 3,
                  crossAxisSpacing: 10,
                  mainAxisSpacing: 10,
                  children: [
                    _buildServiceCard(context, Icons.info_outline, 'ABOUT THE APP', () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (context) => const AboutTheAppPage()),
                      );
                    }),
                    _buildServiceCard(context, Icons.gavel, 'LEGAL - AID', () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (context) => const LegalAidPage()),
                      );
                    }),
                    _buildServiceCard(context, Icons.people_outline, 'LADC/PANEL LAWYER'),
                    _buildServiceCard(context, Icons.menu_book, 'ADR'),
                    _buildServiceCard(context, Icons.check_circle_outline, 'VICTIM COMPENSATION'),
                    _buildServiceCard(context, Icons.group_add, 'SOCIAL ACTION'),
                    _buildServiceCard(context, Icons.lightbulb_outline, 'LEGAL AWARENESS'),
                    _buildServiceCard(context, Icons.shield_outlined, 'KARAHAAT'),
                    _buildServiceCard(context, Icons.handshake_outlined, 'PLV'),
                  ],
                ),
              ],
            ),
          ),

          // Latest Section
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: const LatestSection(),
          ),


        ],
      ),
    );
  }

  Widget _buildServiceCard(BuildContext context, IconData icon, String title, [VoidCallback? onTap]) {
    return Container(
      decoration: BoxDecoration(
        color: const Color.fromARGB(255, 12, 44, 138),
        borderRadius: BorderRadius.circular(10),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.6), // Darker shadow color
            blurRadius: 3, // Adjust blur as needed
            spreadRadius: 1, // Adjust spread as needed
            offset: Offset(0, 2), // X, Y offset
          ),
        ],
      ),
      child: Card(
        color: Colors.transparent, // Make card transparent to show container's color
        elevation: 0, // No elevation from Card, shadow comes from Container
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(10),
        ),
        child: InkWell(
          onTap: onTap,
          splashColor: Colors.white,
          child: SizedBox.expand(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(icon, size: 40, color: Colors.white),
                const SizedBox(height: 8),
                Text(
                  title,
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.bold,
                    fontSize: 12,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

