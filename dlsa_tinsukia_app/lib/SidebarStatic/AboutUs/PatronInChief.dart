import 'package:flutter/material.dart';
import '../../NavigationBar/Sidebar.dart';

class PatronInChiefPage extends StatelessWidget {
  const PatronInChiefPage({super.key});

  @override
  Widget build(BuildContext context) {
    return MainLayout(
      child: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.only(top: 70.0, left: 16.0, right: 16.0, bottom: 90.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: const [
              Text(
                'Patron In Chief',
                style: TextStyle(
                  fontSize: 28,
                  fontWeight: FontWeight.bold,
                ),
                textAlign: TextAlign.center,
              ),
              SizedBox(height: 20),
              Image(
                image: AssetImage('assets/images/SidebarStaticImages/p1.jpeg'), // Placeholder path
                width: double.infinity,
                fit: BoxFit.cover,
              ),
              SizedBox(height: 10),
              Center(
                child: Text(
                  'Hon\'ble Mr. Justice Vijay Bishnoi, Chief Justice',
                  style: TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                    color: Colors.black,
                    // fontStyle: FontStyle.italic,
                  ),
                  textAlign: TextAlign.center,
                ),
              ),
            //   SizedBox(height: 10),
            //   Image(
            //     image: AssetImage('assets/images/SidebarStaticImages/2.jpeg'), // Placeholder path
            //     width: double.infinity,
            //     fit: BoxFit.cover,
            //   ),
              SizedBox(height: 20),
              Text(
                '''
                    Born on 26th March, 1964 in Jodhpur. Enrolled as an advocate on 08th July, 1989. Practiced at the Rajasthan High Court and the Central Administrative Tribunal at Jodhpur. Practiced in wide range of areas such as civil, criminal, Constitutional, service, election cases etc. Served as Additional Central Govt. Standing Counsel during the years 2000-2004; as counsel for Rural Development and Panchayat Raj Department, Stamps & Registration Department, Co- operative Department, Labour Department, Transport Department & Excise Department, Govt. of Rajasthan; as counsel for National Highway Authority of India (NHAI), Rajasthan State Electricity Board, Jodhpur, Vidyut Vitaran Nigam Limited (JVVNL), Ajmer Vidyut Vitaran Nigam Limited (AVVNL), Maharana Pratap University of Agriculture & Technology Udaipur, UCO Bank, Jai Narain Vyas University, Jodhpur, Rajasthan Agriculture Marketing Board and various Central Co-operative Banks; as counsel for Bar Council of India & Bar Council of Rajasthan at Rajasthan High Court, Jodhpur; as counsel for Bharat Sanchar Nigam Limited (BSNL), Rajasthan High Court, Jodhpur and as counsel for Bharat Petroleum Corporation Limited (BPCL), Rajasthan High Court, Jodhpur.


                    Appointed as Additional Judge of the Rajasthan High Court on 08th January, 2013. Took oath as a Permanent Judge of the Rajasthan High Court on 07th January 2015. Appointed as the Chief Justice of Gauhati High Court and took oath as the Chief Justice, Gauhati High Court on 05th February, 2024.
                ''',
                textAlign: TextAlign.justify,
                style: TextStyle(fontSize: 16),
              ),
            ],
          ),
        ),
      ),
    );
  }
}