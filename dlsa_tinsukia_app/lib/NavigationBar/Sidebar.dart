import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../main.dart';
// import '../pages/Legal_Aid.dart'; // path
import '../DisplayContent/DisplayContent.dart';
import '../Categories/Achievements.dart';
import '../Categories/ActivityCalender.dart';
import '../Categories/Advertisement.dart';
import '../Categories/AwarenessMeeting.dart';
import '../Categories/AwarenessProgram.dart';
import '../Categories/JuvenileJustice.dart';
import '../Categories/LegalAid.dart';
import '../Categories/LegalAssistance.dart';
import '../Categories/LegalLiteracyClasses.dart';
import '../Categories/LokAdalat.dart';
import '../Categories/Mediation.dart';
import '../Categories/MonitoringLegalClinic.dart';
import '../Categories/MonitoringJail.dart';
import '../Categories/Meeting.dart';
import '../Categories/Notice.dart';
import '../Categories/Observance.dart';
import '../Categories/Results.dart';
import '../Categories/Schemes.dart';
import '../Categories/VictimCompensation.dart';
import '../Categories/Workshop.dart';
import '../Categories/Recruitment.dart';
import '../SidebarStatic/AboutUs/Introduction.dart';
import '../SidebarStatic/AboutUs/PatronInChief.dart';

class FooterWidget extends StatelessWidget {
  const FooterWidget({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: EdgeInsets.only(top: 10, bottom: 20),
      color: const Color.fromARGB(255, 12, 44, 138),
      child: Column(
        children: [
          RichText(
            textAlign: TextAlign.center,
            text: TextSpan(
              children: [
                TextSpan(
                  text: 'Copyright (c) 2025 ',
                  style: TextStyle(
                    color: Colors.white,
                    fontFamily: 'Cambria',
                    fontSize: 14,
                  ),
                ),
                TextSpan(
                  text: 'District Legal Services Authority, Tinsukia',
                  style: TextStyle(
                    color: Colors.red,
                    fontFamily: 'Cambria',
                    fontSize: 14,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
          ),
          SizedBox(height: 4),
          Text(
            'All Rights Reserved',
            style: TextStyle(
              color: Colors.white,
              fontFamily: 'Cambria',
              fontSize: 14,
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }
}

class MainLayout extends StatefulWidget {
  final Widget child;

  MainLayout({super.key, required this.child});

  @override
  State<MainLayout> createState() => _MainLayoutState();
}

class _MainLayoutState extends State<MainLayout> {
  bool _isDropdownVisible = true;
  bool _isDropdownOpen = false;
  final ScrollController _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    _scrollController.addListener(_scrollListener);
  }

  @override
  void dispose() {
    _scrollController.removeListener(_scrollListener);
    _scrollController.dispose();
    super.dispose();
  }

  void _scrollListener() {
    if (_scrollController.offset > 100 && _isDropdownVisible) {
      setState(() {
        _isDropdownVisible = false;
      });
    } else if (_scrollController.offset <= 100 && !_isDropdownVisible) {
      setState(() {
        _isDropdownVisible = true;
      });
    }
  }

  // First Top Bar for Hamburger Menu
  @override
  Widget build(BuildContext context) {
    return Scaffold(


      appBar: PreferredSize(
        preferredSize: Size.fromHeight(kToolbarHeight),
        child: SafeArea(
          child: AppBar(
            titleSpacing: 0,
            leading: Builder(
              builder: (BuildContext context) {
                return IconButton(
                  icon: const Icon(Icons.menu),
                  onPressed: () {
                    Scaffold.of(context).openDrawer();
                  },
                );
              },
            ),
            title: Container(
              padding: EdgeInsets.symmetric(horizontal: 8.0),
              child: Text(
                'District Legal Service Authority, Tinsukia, Assam',
                style: TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                  fontFamily: 'Cambria',
                  fontSize: 22,
                ),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                // textAlign: TextAlign.center,
              ),
            ),
            flexibleSpace: Container(
              decoration: BoxDecoration(
                color: const Color.fromARGB(255, 12, 44, 138),
              ),
            ),
            backgroundColor: Colors.transparent,
            iconTheme: const IconThemeData(color: Colors.white, size: 30),
          ),
        ),
      ),




      drawer: Drawer(
        child: SafeArea( // 👈 Wrap with SafeArea to avoid overlap
          child: Theme(
              data: Theme.of(context).copyWith(
                textTheme: Theme.of(context).textTheme.apply(
                  fontFamily: 'Cambria',
                ),
              ),
            child: Container(
              color: const Color.fromARGB(255, 12, 44, 138), // 👈 Set drawer background color here
              
              child: Column(
                children: [


                  // Main scrollable items
                  Expanded(
                    child: ListView(
                    padding: EdgeInsets.zero,
                      children: <Widget>[
                        ListTile(
                          leading: Icon(Icons.home , color: Colors.white),
                          title: Text(
                            'HOME' , 
                            style: TextStyle(color: Colors.white , fontWeight: FontWeight.bold),
                            ),
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(builder: (context) => HomeScreen()),
                              );
                            }, 
                        ),

                        ExpansionTile(
                          leading: Icon(Icons.info, color: Colors.white),
                          title: Text(
                            'ABOUT US',
                            style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                          ),
                          collapsedIconColor: Color.fromARGB(255, 255, 255, 255),  // White arrow when collapsed
                          iconColor: Colors.white,    // White arrow when expanded
                          children: [
                            Padding(
                              padding: EdgeInsets.zero,
                              child: Column(
                                children: [
                                  Container(
                                    width: double.infinity,
                                    color: const Color.fromARGB(255, 18, 53, 158),
                                    child: ListTile(
                                      contentPadding: EdgeInsets.symmetric(horizontal: 32.0),
                                      title: Text(
                                        'Introduction',
                                        style: TextStyle(color: Colors.white),
                                      ),
                                      onTap: () {
                                        Navigator.push(
                                          context,
                                          MaterialPageRoute(builder: (context) => const IntroductionPage()),
                                        );
                                      }, 
                                    ),
                                  ),
                                  Container(
                                    width: double.infinity,
                                    color: const Color.fromARGB(255, 18, 53, 158),
                                    child: ListTile(
                                      contentPadding: EdgeInsets.symmetric(horizontal: 32.0),
                                      title: Text(
                                        'Patron in Chief',
                                        style: TextStyle(color: Colors.white),
                                      ),
                                      onTap: () {
                                        Navigator.push(
                                          context,
                                          MaterialPageRoute(builder: (context) => const PatronInChiefPage()),
                                        );
                                      }, 
                                    ),
                                  ),
                                  Container(
                                    width: double.infinity,
                                    color: const Color.fromARGB(255, 18, 53, 158),
                                    child: ListTile(
                                      contentPadding: EdgeInsets.symmetric(horizontal: 32.0),
                                      title: Text(
                                        'Executive Chairman',
                                        style: TextStyle(color: Colors.white),
                                      ),
                                      onTap: () {
                                        // Add navigation logic if needed
                                      }, 
                                    ),
                                  ),
                                  Container(
                                    width: double.infinity,
                                    color: const Color.fromARGB(255, 18, 53, 158),
                                    child: ListTile(
                                      contentPadding: EdgeInsets.symmetric(horizontal: 32.0),
                                      title: Text(
                                        'Member Secretary',
                                        style: TextStyle(color: Colors.white),
                                      ),
                                      onTap: () {
                                        // Add navigation logic if needed
                                      }, 
                                    ),
                                  ),
                                  Container(
                                    width: double.infinity,
                                    color: const Color.fromARGB(255, 18, 53, 158),
                                    child: ListTile(
                                      contentPadding: EdgeInsets.symmetric(horizontal: 32.0),
                                      title: Text(
                                        'Chairman',
                                        style: TextStyle(color: Colors.white),
                                      ),
                                      onTap: () {
                                        // Add navigation logic if needed
                                      }, 
                                    ),
                                  ),
                                  Container(
                                    width: double.infinity,
                                    color: const Color.fromARGB(255, 18, 53, 158),
                                    child: ListTile(
                                      contentPadding: EdgeInsets.symmetric(horizontal: 32.0),
                                      title: Text(
                                        'Secretary',
                                        style: TextStyle(color: Colors.white),
                                      ),
                                      onTap: () {
                                        // Add navigation logic if needed
                                      }, 
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),

                        ExpansionTile(
                          leading: Icon(Icons.rule, color: Colors.white),
                          title: Text(
                            'ACTS & RULES',
                            style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                          ),
                          collapsedIconColor: Color.fromARGB(255, 255, 255, 255),  // White arrow when collapsed
                          iconColor: Colors.white,    // White arrow when expanded
                          children: [
                            Padding(
                              padding: EdgeInsets.zero,
                              child: Column(
                                children: [
                                  Container(
                                    width: double.infinity,
                                    color: const Color.fromARGB(255, 18, 53, 158),
                                    child: ListTile(
                                      contentPadding: EdgeInsets.symmetric(horizontal: 32.0),
                                      title: Text(
                                        'The Legal Services Authorities Act 1987',
                                        style: TextStyle(color: Colors.white),
                                      ),
                                      onTap: () {
                                        // Add navigation logic if needed
                                      }, 
                                    ),
                                  ),
                                  Container(
                                    width: double.infinity,
                                    color: const Color.fromARGB(255, 18, 53, 158),
                                    child: ListTile(
                                      contentPadding: EdgeInsets.symmetric(horizontal: 32.0),
                                      title: Text(
                                        'Rules',
                                        style: TextStyle(color: Colors.white),
                                      ),
                                      onTap: () {
                                        // Add navigation logic if needed
                                      }, 
                                    ),
                                  ),
                                  Container(
                                    width: double.infinity,
                                    color: const Color.fromARGB(255, 18, 53, 158),
                                    child: ListTile(
                                      contentPadding: EdgeInsets.symmetric(horizontal: 32.0),
                                      title: Text(
                                        'Regulation',
                                        style: TextStyle(color: Colors.white),
                                      ),
                                      onTap: () {
                                        // Add navigation logic if needed
                                      }, 
                                    ),
                                  ),
                                  Container(
                                    width: double.infinity,
                                    color: const Color.fromARGB(255, 18, 53, 158),
                                    child: ListTile(
                                      contentPadding: EdgeInsets.symmetric(horizontal: 32.0),
                                      title: Text(
                                        'Schemes',
                                        style: TextStyle(color: Colors.white),
                                      ),
                                      onTap: () {
                                        // Add navigation logic if needed
                                      }, 
                                    ),
                                  ),
                                  Container(
                                    width: double.infinity,
                                    color: const Color.fromARGB(255, 18, 53, 158),
                                    child: ListTile(
                                      contentPadding: EdgeInsets.symmetric(horizontal: 32.0),
                                      title: Text(
                                        'Guidelines',
                                        style: TextStyle(color: Colors.white),
                                      ),
                                      onTap: () {
                                        // Add navigation logic if needed
                                      }, 
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),

                        ExpansionTile(
                          leading: Icon(Icons.electrical_services, color: Colors.white),
                          title: Text(
                            'SERVICES',
                            style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                          ),
                          collapsedIconColor: Color.fromARGB(255, 255, 255, 255),  // White arrow when collapsed
                          iconColor: Colors.white,    // White arrow when expanded
                          children: [
                            Padding(
                              padding: EdgeInsets.zero,
                              child: Column(
                                children: [
                                  Container(
                                    width: double.infinity,
                                    color: const Color.fromARGB(255, 18, 53, 158),
                                    child: ListTile(
                                      contentPadding: EdgeInsets.symmetric(horizontal: 32.0),
                                      title: Text(
                                        'Legal Aid',
                                        style: TextStyle(color: Colors.white),
                                      ),
                                      onTap: () {
                                        Navigator.push(
                                          context,
                                          MaterialPageRoute(builder: (context) => const LegalAidPage()),
                                        );
                                      },
                                    ),
                                  ),
                                  Container(
                                    width: double.infinity,
                                    color: const Color.fromARGB(255, 18, 53, 158),
                                    child: ListTile(
                                      contentPadding: EdgeInsets.symmetric(horizontal: 32.0),
                                      title: Text(
                                        'Lok Adalats',
                                        style: TextStyle(color: Colors.white,fontSize: 16),
                                      ),
                                      onTap: () {
                                        Navigator.push(
                                          context,
                                          MaterialPageRoute(builder: (context) => const LokAdalatPage()),
                                        );
                                      }, 
                                    ),
                                  ),
                                  Container(
                                    width: double.infinity,
                                    color: const Color.fromARGB(255, 18, 53, 158),
                                    child: ListTile(
                                      contentPadding: EdgeInsets.symmetric(horizontal: 32.0),
                                      title: Text(
                                        'Legal Assistance',
                                        style: TextStyle(color: Colors.white),
                                      ),
                                      onTap: () {
                                        Navigator.push(
                                          context,
                                          MaterialPageRoute(builder: (context) => const LegalAssistancePage()),
                                        );
                                      },
                                    ),
                                  ),
                                  Container(
                                    width: double.infinity,
                                    color: const Color.fromARGB(255, 18, 53, 158),
                                    child: ListTile(
                                      contentPadding: EdgeInsets.symmetric(horizontal: 32.0),
                                      title: Text(
                                        'Legal Literacy Classes',
                                        style: TextStyle(color: Colors.white),
                                      ),
                                      onTap: () {
                                        Navigator.push(
                                          context,
                                          MaterialPageRoute(builder: (context) => const LegalLiteracyClassesPage()),
                                        );
                                      },
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),



                        ExpansionTile(
                          leading: Icon(Icons.model_training, color: Colors.white),
                          title: Text(
                            'TRAINING MATERIALS',
                            style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                          ),
                          collapsedIconColor: Color.fromARGB(255, 255, 255, 255),  // White arrow when collapsed
                          iconColor: Colors.white,    // White arrow when expanded
                          children: [
                            Padding(
                              padding: EdgeInsets.zero,
                              child: Column(
                                children: [
                                  Container(
                                    width: double.infinity,
                                    color: const Color.fromARGB(255, 18, 53, 158),
                                    child: ListTile(
                                      contentPadding: EdgeInsets.symmetric(horizontal: 32.0),
                                      title: Text(
                                        'LSMS & LSCMS',
                                        style: TextStyle(color: Colors.white),
                                      ),
                                      onTap: () {
                                        // Add navigation logic if needed
                                      }, 
                                    ),
                                  ),                         
                                ],
                              ),
                            ),
                          ],
                        ),



                        ExpansionTile(
                          leading: Icon(Icons.book, color: Colors.white),
                          title: Text(
                            'APPLICATION/CASE STUDY',
                            style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                          ),
                          collapsedIconColor: Color.fromARGB(255, 255, 255, 255),  // White arrow when collapsed
                          iconColor: Colors.white,    // White arrow when expanded
                          children: [
                            Padding(
                              padding: EdgeInsets.zero,
                              child: Column(
                                children: [
                                  Container(
                                    width: double.infinity,
                                    color: const Color.fromARGB(255, 18, 53, 158),
                                    child: ListTile(
                                      contentPadding: EdgeInsets.symmetric(horizontal: 32.0),
                                      title: Text(
                                        'Pre-Litigation',
                                        style: TextStyle(color: Colors.white),
                                      ),
                                      onTap: () {
                                        // Add navigation logic if needed
                                      },                               
                                    ),
                                  ),                         
                                ],
                              ),
                            ),
                          ],
                        ),







                      ],
                    ),
                  ),






                  // Bottom fixed "Contact Us"
                  // Bottom fixed "Contact Us"
                  Container(
                    color: const Color.fromARGB(255, 18, 53, 158),
                    child: ListTile(
                            leading: Icon(Icons.mail, color: Colors.white),
                            title: Text(
                              'Contact Us',
                              style: TextStyle(
                                color: Colors.white,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            onTap: () {
                              // Add navigation logic if needed
                            },
                        ),
                  ),
                        
                  

                ],
              ),

            ),
          )
        ),
      ),



      
      body: Stack(
        children: [
          // Main content
          Column(
            children: [
              Expanded(
                child: SingleChildScrollView(
                  child: ConstrainedBox(
                    constraints: BoxConstraints(
                      minHeight: (MediaQuery.of(context).size.height - kToolbarHeight - 50) > 0 // substract app bar and quick link height
                          ? MediaQuery.of(context).size.height - kToolbarHeight - 50
                          : 0, // Ensure minimum height is never negative
                    ),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        widget.child,
                        FooterWidget(),
                      ],
                    ),
                  ),
                ),
              ),
            ],
          ),
          // White background for Quick Links
          Positioned(
            top: 0,
            left: 0,
            right: 0,
            child: Container(
              color: Colors.white,
              height: 50,
            ),
          ),
          // Quick Links dropdown
          Positioned(
            top: 4,
            left: 0,
            right: 0,
            child: Theme(
              data: Theme.of(context).copyWith(
                textTheme: Theme.of(context).textTheme.apply(
                  fontFamily: 'Cambria',
                ),
              ),
              child: ClipRect(
                child: AnimatedContainer(
                  duration: Duration(milliseconds: 400),
                  height: _isDropdownVisible ? (_isDropdownOpen ? 560 : 40) : 0,
                  color: const Color.fromARGB(255, 12, 44, 138),
                  child: _isDropdownVisible
                      ? Column(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Container(
                              height: 40,
                              color: const Color.fromARGB(255, 12, 44, 138),
                              child: GestureDetector(
                                onTap: () {
                                  setState(() {
                                    _isDropdownOpen = !_isDropdownOpen;
                                  });
                                },
                                child: Row(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Icon(Icons.menu, color: Colors.white, size: 30),
                                    SizedBox(width: 8),
                                    Text(
                                      'Quick Links',
                                      style: TextStyle(
                                        color: Colors.white,
                                        fontWeight: FontWeight.bold,
                                        fontSize: 18,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                            if (_isDropdownOpen)
                              Container(
                              color: const Color.fromARGB(255, 18, 53, 158),
                                child: Padding(
                                  padding: EdgeInsets.symmetric(horizontal: 16.0, vertical: 8.0),
                                  child: Row(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    mainAxisAlignment: MainAxisAlignment.start,
                                    children: [
                                      Expanded(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: -3),
                                              leading: Icon(Icons.emoji_events, color: Colors.white),
                                              title: Text(
                                                'Achievement',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () { // Navigation Logic
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const AchievementsPage()),
                                                );
                                              },
                                            ),
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: 2),
                                              leading: Icon(Icons.calendar_today, color: Colors.white),
                                              title: Text(
                                                'Activity Calender',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () { // Navigation Logic
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const ActivityCalender()),
                                                );
                                              }, 
                                            ),
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: -3),
                                              leading: Icon(Icons.campaign, color: Colors.white),
                                              title: Text(
                                                'Advertisement',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () { // Navigation Logic
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const AdvertisementPage()),
                                                );
                                              },
                                            ),
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: 3),
                                              leading: Icon(Icons.groups, color: Colors.white),
                                              title: Text(
                                                'Awareness Meeting',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () {
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const AwarenessMeetingPage()),
                                                );
                                              },
                                            ),
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: 3),
                                              leading: Icon(Icons.school, color: Colors.white),
                                              title: Text(
                                                'Awareness Program',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () {
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const AwarenessProgramPage()),
                                                );
                                              },
                                            ),
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: 3),
                                              leading: Icon(Icons.child_care, color: Colors.white),
                                              title: Text(
                                                'Juvenile Justice',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () {
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const JuvenileJusticePage()),
                                                );
                                              },
                                            ),
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: -3),
                                              leading: Icon(Icons.gavel, color: Colors.white),
                                              title: Text(
                                                'Legal Aid',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () {
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const LegalAidPage()),
                                                );
                                              },
                                            ),
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: 3),
                                              leading: Icon(Icons.support_agent, color: Colors.white),
                                              title: Text(
                                                'Legal Assistance',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () {
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const LegalAssistancePage()),
                                                );
                                              },
                                            ),
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: -3),
                                              leading: Icon(Icons.balance, color: Colors.white),
                                              title: Text(
                                                'Lok Adalat',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () {
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const LokAdalatPage()),
                                                );
                                              },
                                            ),
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: 3),
                                              leading: Icon(Icons.menu_book, color: Colors.white),
                                              title: Text(
                                                'Legal Literacy Classes',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () {
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const LegalLiteracyClassesPage()),
                                                );
                                              },
                                            ),
                                          ],
                                        ),
                                      ),
                                      SizedBox(width: 16),
                                      Expanded(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: -3),
                                              leading: Icon(Icons.handshake, color: Colors.white),
                                              title: Text(
                                                'Mediation',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () {
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const MediationPage()),
                                                );
                                              },
                                            ),
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: 3),
                                              leading: Icon(Icons.medical_services, color: Colors.white),
                                              title: Text(
                                                'Monitoring Legal Clinic',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () {
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const MonitoringLegalClinicPage()),
                                                );
                                              },                                   
                                            ),
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: 3),
                                              leading: Icon(Icons.security, color: Colors.white),
                                              title: Text(
                                                'Monitoring Jail',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () {
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const MonitoringJailPage()),
                                                );
                                              },
                                            ),
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: -3),
                                              leading: Icon(Icons.people, color: Colors.white),
                                              title: Text(
                                                'Meeting',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () {
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const MeetingPage()),
                                                );
                                              },
                                            ),
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: -3),
                                              leading: Icon(Icons.notifications, color: Colors.white),
                                              title: Text(
                                                'Notice',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () {
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const NoticePage()),
                                                );
                                              },
                                            ),
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: -3),
                                              leading: Icon(Icons.event, color: Colors.white),
                                              title: Text(
                                                'Observance',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () {
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const ObservancePage()),
                                                );
                                              },
                                            ),
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: -3),
                                              leading: Icon(Icons.assessment, color: Colors.white),
                                              title: Text(
                                                'Results',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () {
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const ResultsPage()),
                                                );
                                              },
                                            ),
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: -3),
                                              leading: Icon(Icons.assignment, color: Colors.white),
                                              title: Text(
                                                'Schemes',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () {
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const SchemesPage()),
                                                );
                                              },
                                            ),
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: 3),
                                              leading: Icon(Icons.payments, color: Colors.white),
                                              title: Text(
                                                'Victim Compensation',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () {
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const VictimCompensationPage()),
                                                );
                                              },
                                            ),
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: -3),
                                              leading: Icon(Icons.workspaces, color: Colors.white),
                                              title: Text(
                                                'Workshop',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () {
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const WorkshopPage()),
                                                );
                                              },
                                            ),  
                                            ListTile(
                                              dense: true,
                                              minVerticalPadding: 0,
                                              contentPadding: EdgeInsets.symmetric(vertical: 0, horizontal: 15),
                                              visualDensity: VisualDensity(vertical: -3),
                                              leading: Icon(Icons.work, color: Colors.white),
                                              title: Text(
                                                'Recruitment',
                                                style: TextStyle(color: Colors.white, fontSize: 16),
                                              ),
                                              onTap: () {
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(builder: (context) => const RecruitmentPage()),
                                                );
                                              },
                                            ),
                                                                                  
                                          ],
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                          ],
                        )
                      : SizedBox.shrink(),
                ),
              ),
            ),
          ),
        ],
      ),
     
    );
    
  }
}





























