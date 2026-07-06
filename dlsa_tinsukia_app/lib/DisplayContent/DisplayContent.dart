import 'package:flutter/material.dart';
import 'dart:io';
import '../NavigationBar/Sidebar.dart'; // Adjust the path if needed
import 'package:cached_network_image/cached_network_image.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:pdfx/pdfx.dart';
import 'package:http/http.dart' as http;
import 'package:flutter_html/flutter_html.dart';

class DisplaycontentPage extends StatefulWidget {
  final Map<String, dynamic> achievement;
  final String categoryName;
  final int currentIndex;
  final List<Map<String, dynamic>> itemsList;

  const DisplaycontentPage({
    super.key,
    required this.achievement,
    required this.categoryName,
    required this.currentIndex,
    required this.itemsList,
  });

  @override
  State<DisplaycontentPage> createState() => _DisplaycontentPageState();
}

class _DisplaycontentPageState extends State<DisplaycontentPage> {
  Map<String, int> retryCounts = {};
  Map<String, PdfController> pdfControllers = {};
  bool isLoadingPdfs = true;

  @override
  void initState() {
    super.initState();
    initializePdfControllers();
  }

  Future<void> initializePdfControllers() async {
    final pdfs = widget.achievement['pdfs'] as List<dynamic>? ?? [];
    final String baseUrl = Platform.isAndroid 
        ? 'http://10.0.2.2:8000'     // Android Emulator
        // ? 'http://192.168.1.5:8000'  // Physical Android device use pc ipaddress
        : 'http://localhost:8000'; // iOS Simulator or other platforms

    for (var pdf in pdfs) {
      final pdfUrl = '$baseUrl/storage/${pdf['pdf_path']}';
      if (!pdfControllers.containsKey(pdfUrl)) {
        try {
          final response = await http.get(Uri.parse(pdfUrl));
          if (response.statusCode == 200) {
            pdfControllers[pdfUrl] = PdfController(
              document: PdfDocument.openData(response.bodyBytes),
            );
          }
        } catch (e) {
          print('Error loading PDF: $e');
        }
      }
    }
    if (mounted) {
      setState(() {
        isLoadingPdfs = false;
      });
    }
  }

  @override
  void dispose() {
    // Dispose all PDF controllers
    pdfControllers.values.forEach((controller) => controller.dispose());
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final String baseUrl = Platform.isAndroid 
        ? 'http://10.0.2.2:8000'     // Android Emulator
        // ? 'http://192.168.1.5:8000'  // Physical Android device use pc ipaddress
        : 'http://localhost:8000'; // iOS Simulator or other platforms
    final pictures = widget.achievement['pictures'] as List<dynamic>? ?? [];
    final links = widget.achievement['links'] as List<dynamic>? ?? [];
    final pdfs = widget.achievement['pdfs'] as List<dynamic>? ?? [];

    // Dynamic debug print for all keys/values in the achievement map
    print('--- DisplaycontentPage Debug ---');
    widget.achievement.forEach((key, value) {
      print('$key: $value');
    });
    print('-------------------------------');

    return MainLayout(
      child: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Add top padding to move content down
              const SizedBox(height: 50),
              
              // Back to Achievements
              GestureDetector(
                onTap: () => Navigator.pop(context),
                child: Text(
                  '← ${widget.categoryName}',
                  style: TextStyle(
                    color: Colors.blue,
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                    decoration: TextDecoration.underline,
                  ),
                ),
              ),
              const SizedBox(height: 24),

              // Title
              Text(
                widget.achievement['title'] ?? 'No Title',
                style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 12),

              // Upload Date
              if (widget.achievement['upload_date'] != null)
                Text(
                  'Uploaded on: ${widget.achievement['upload_date']}',
                  style: TextStyle(color: Colors.grey[700], fontSize: 16),
                ),
              const SizedBox(height: 16),

              // Pictures
              if (pictures.isNotEmpty)
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // const Text('Pictures:', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 8),
                    ...pictures.map((pic) => Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            if (pic['picture_path'] != null)
                              InkWell(
                                onTap: () {
                                  // Do something on tap, e.g., show fullscreen image
                                },
                                child: ClipRRect(
                                  borderRadius: BorderRadius.circular(8),
                                  child: Container(
                                    margin: const EdgeInsets.only(bottom: 10),
                                    child: CachedNetworkImage(
                                      imageUrl: '$baseUrl/storage/${pic['picture_path']}',
                                      height: 180,
                                      width: double.infinity,
                                      fit: BoxFit.cover,
                                      placeholder: (context, url) => Container(
                                        width: double.infinity,
                                        height: 180,
                                        color: Colors.grey[200],
                                        child: const Center(child: CircularProgressIndicator()),
                                      ),
                                      errorWidget: (context, url, error) {
                                        final imageUrl = pic['picture_path'];
                                        retryCounts[imageUrl] = (retryCounts[imageUrl] ?? 0) + 1;

                                        if (retryCounts[imageUrl]! <= 5) {
                                          Future.delayed(const Duration(seconds: 1), () {
                                            if (mounted) setState(() {});
                                          });
                                          return Container(
                                            width: double.infinity,
                                            height: 180,
                                            color: Colors.grey[200],
                                            child: Column(
                                              mainAxisAlignment: MainAxisAlignment.center,
                                              children: [
                                                const CircularProgressIndicator(),
                                                const SizedBox(height: 4),
                                                Text(
                                                  'Retrying... (${retryCounts[imageUrl]}/5)',
                                                  style: TextStyle(
                                                    color: Colors.blue[700],
                                                    fontSize: 12,
                                                  ),
                                                ),
                                              ],
                                            ),
                                          );
                                        }

                                        return Container(
                                          width: double.infinity,
                                          height: 180,
                                          color: Colors.grey[200],
                                          child: Column(
                                            mainAxisAlignment: MainAxisAlignment.center,
                                            children: const [
                                              Icon(Icons.error_outline, color: Colors.red),
                                              SizedBox(height: 4),
                                              Text(
                                                'Failed to load',
                                                style: TextStyle(
                                                  color: Colors.blue,
                                                  fontSize: 12,
                                                ),
                                              ),
                                            ],
                                          ),
                                        );
                                      },
                                    ),
                                  ),
                                ),
                              ),
                            if (pic['picture_title'] != null)
                              Padding(
                                padding: const EdgeInsets.only(top: 0, bottom: 10),
                                child: Center(
                                  child: Text(
                                    pic['picture_title'],
                                    style: const TextStyle(
                                      fontStyle: FontStyle.italic,
                                      color: Colors.black87,
                                    ),
                                  ),
                                ),
                              ),
                          ],
                        )),
                  ],
                ),


                // PDFs
              if (pdfs.isNotEmpty)
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // const Text('Documents:', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 8),
                    if (isLoadingPdfs)
                      const Center(child: CircularProgressIndicator())
                    else
                      ...pdfs.map((pdf) {
                        final pdfUrl = '$baseUrl/storage/${pdf['pdf_path']}';
                        return Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Container(
                              height: 400,
                              decoration: BoxDecoration(
                                border: Border.all(color: Colors.grey),
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: ClipRRect(
                                borderRadius: BorderRadius.circular(8),
                                child: pdfControllers[pdfUrl] != null
                                    ? PdfView(
                                        controller: pdfControllers[pdfUrl]!,
                                        scrollDirection: Axis.vertical,
                                        builders: PdfViewBuilders<DefaultBuilderOptions>(
                                          options: const DefaultBuilderOptions(),
                                          documentLoaderBuilder: (_) => const Center(child: CircularProgressIndicator()),
                                          pageLoaderBuilder: (_) => const Center(child: CircularProgressIndicator()),
                                          errorBuilder: (_, error) => Center(
                                            child: Text('Error: ${error.toString()}'),
                                          ),
                                        ),
                                      )
                                    : const Center(
                                        child: Text('Failed to load PDF'),
                                      ),
                              ),
                            ),
                            // if (pdf['pdf_title'] != null)
                            //   Padding(
                            //     padding: const EdgeInsets.only(top: 8),
                            //     child: Text(
                            //       pdf['pdf_title'],
                            //       style: const TextStyle(
                            //         fontSize: 16,
                            //         fontWeight: FontWeight.bold,
                            //       ),
                            //     ),
                            //   ),
                            GestureDetector(
                              onTap: () {
                                final url = '$baseUrl/storage/${pdf['pdf_path']}';
                                final uri = Uri.parse(url);
                                launchUrl(uri, mode: LaunchMode.externalApplication);
                              },
                              child: Padding(
                                padding: const EdgeInsets.only(top: 8, bottom: 16),
                                child: Row(
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    Icon(Icons.open_in_browser, 
                                      size: 16, 
                                      color: Colors.blue[700]
                                    ),
                                    const SizedBox(width: 4),
                                    Text(
                                      pdf['pdf_title'] ?? 'View PDF in Browser',
                                      style: TextStyle(
                                        color: Colors.blue[700],
                                        decoration: TextDecoration.underline,
                                        fontSize: 14,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ],
                        );
                      }).toList(),
                  ],
                ),
              

              // Event Date
              const SizedBox(height: 16),
              if (widget.achievement['event_date'] != null)
                RichText(
                  text: TextSpan(
                    children: [
                      const TextSpan(
                        text: 'Event Date: ',
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          color: Colors.black,
                          fontSize: 16,
                        ),
                      ),
                      TextSpan(
                        text: widget.achievement['event_date'],
                        style: TextStyle(
                          color: Colors.grey[700],
                          fontSize: 16,
                        ),
                      ),
                    ],
                  ),
                ),
              const SizedBox(height: 12),

              // Location
              if (widget.achievement['location'] != null)
                Text(
                  'Location: ${widget.achievement['location']}',
                  style: TextStyle(color: Colors.grey[700], fontSize: 16),
                ),
              const SizedBox(height: 12),

              // Description
              if (widget.achievement['description'] != null)
                Html(
                  data: widget.achievement['description'],
                  style: {
                    "body": Style(
                      fontSize: FontSize(16),
                      color: Colors.black87,
                    ),
                    "h1": Style(
                      fontSize: FontSize(24),
                      fontWeight: FontWeight.bold,
                      margin: Margins.only(bottom: 16),
                    ),
                    "p": Style(
                      margin: Margins.only(bottom: 12),
                    ),
                    "a": Style(
                      color: Colors.blue,
                      textDecoration: TextDecoration.underline,
                    ),
                  },
                  onLinkTap: (url, _, __) {
                    if (url != null) {
                      final uri = Uri.parse(url);
                      launchUrl(uri, mode: LaunchMode.externalApplication);
                    }
                  },
                ),
              const SizedBox(height: 16),

              // Links
              if (links.isNotEmpty)
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // const Text('Links:', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                    ...links.map((link) => Padding(
                          padding: const EdgeInsets.only(top: 4),
                          child: GestureDetector(
                            onTap: () {
                              final url = link['link_url'];
                              if (url != null) {
                                final uri = Uri.parse(url);
                                launchUrl(uri, mode: LaunchMode.externalApplication);
                              }
                            },
                            child: Text(
                              link['link_title'] ?? 'Link',
                              style: const TextStyle(
                                color: Colors.blue,
                                decoration: TextDecoration.underline,
                                fontSize: 16,
                              ),
                            ),
                          ),
                        )),
                  ],
                ),

              const SizedBox(height: 16),

              

              const SizedBox(height: 32),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  ElevatedButton(
                    onPressed: widget.currentIndex > 0
                        ? () {
                            Navigator.pushReplacement(
                              context,
                              MaterialPageRoute(
                                builder: (context) => DisplaycontentPage(
                                  achievement: widget.itemsList[widget.currentIndex - 1],
                                  categoryName: widget.categoryName,
                                  currentIndex: widget.currentIndex - 1,
                                  itemsList: widget.itemsList,
                                ),
                              ),
                            );
                          }
                        : null,
                    child: const Text('Previous'),
                  ),
                  ElevatedButton(
                    onPressed: widget.currentIndex < widget.itemsList.length - 1
                        ? () {
                            Navigator.pushReplacement(
                              context,
                              MaterialPageRoute(
                                builder: (context) => DisplaycontentPage(
                                  achievement: widget.itemsList[widget.currentIndex + 1],
                                  categoryName: widget.categoryName,
                                  currentIndex: widget.currentIndex + 1,
                                  itemsList: widget.itemsList,
                                ),
                              ),
                            );
                          }
                        : null,
                    child: const Text('Next'),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}