import 'package:flutter/material.dart';
import '../../NavigationBar/Sidebar.dart';

class IntroductionPage extends StatelessWidget {
  const IntroductionPage({super.key});

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
                'Introduction',
                style: TextStyle(
                  fontSize: 28,
                  fontWeight: FontWeight.bold,
                ),
                textAlign: TextAlign.center,
              ),
              SizedBox(height: 20),
              Image(
                image: AssetImage('assets/images/SidebarStaticImages/1.jpeg'), // Placeholder path
                width: double.infinity,
                fit: BoxFit.cover,
              ),
              SizedBox(height: 10),
              Center(
                child: Text(
                  'In the photograph: Hon\'ble Mr Justice Ranjan Gogoi, the then Judge of the Supreme of India can be seen inaugurating the ADR Centre, Tinsukia',
                  style: TextStyle(
                    fontSize: 12,
                    color: Colors.grey,
                    fontStyle: FontStyle.italic,
                  ),
                  textAlign: TextAlign.center,
                ),
              ),
              SizedBox(height: 10),
              Image(
                image: AssetImage('assets/images/SidebarStaticImages/2.jpeg'), // Placeholder path
                width: double.infinity,
                fit: BoxFit.cover,
              ),
              SizedBox(height: 20),
              Text(
                '''The District Legal Services Authority, Tinsukia was established with the motto of providing "access to justice for all". The new building of DLSA, Tinsukia i.e., the ADR Centre was inaugurated on 23/12/2015 inside the court campus of Tinsukia District Judiciary. Since then the DLSA, Tinsukia has got its full time Secretary. Prior to that, the DLSA, Tinsukia was attached to the office of Learned Civil Judge (Sr. Div) Cum Assistant Sessions Judge, Tinsukia. The District Legal Services Authority, Tinsukia in order to achieve its motto provides free and competent legal services to the beneficiaries in addition to other facilities in accordance with the regulations framed by National Legal Services Authority and Assam State Legal Services Authority. At present, the Hon'ble District & Sessions Judge, Tinsukia is the Chairman of District Legal Services Authority, Tinsukia. In addition to that, the District Legal Services Authority comprises of one full time Secretary and four permanent staffs.''',
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