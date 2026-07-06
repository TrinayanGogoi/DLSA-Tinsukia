import 'package:flutter/material.dart';
import '../NavigationBar/Sidebar.dart';
import 'dart:convert';
import 'dart:typed_data';
import 'dart:async';
import 'package:http/http.dart' as http;
import 'package:cached_network_image/cached_network_image.dart';
import 'dart:io';
import '../DisplayContent/DisplayContent.dart';

class WorkshopPage extends StatefulWidget {
  const WorkshopPage({super.key});

  @override
  State<WorkshopPage> createState() => _WorkshopPageState();
}

class _WorkshopPageState extends State<WorkshopPage> {
  List<Map<String, dynamic>> workshops = [];
  bool isLoading = true;
  Map<String, int> retryCounts = {};
  
  final String baseUrl = Platform.isAndroid 
      ? 'http://10.0.2.2:8000'     // Android Emulator
      // ? 'http://192.168.1.5:8000'  // Physical Android device use pc ipaddress
      : 'http://localhost:8000'; // iOS Simulator or other platforms

  @override
  void initState() {
    super.initState();
    fetchWorkshops();
  }

  Future<void> fetchWorkshops() async {
    try {
      print('Fetching data from: $baseUrl');
      
      final uploadsResponse = await http.get(Uri.parse('$baseUrl/api/uploads/Retrieve'));
      final tagsResponse = await http.get(Uri.parse('$baseUrl/api/tags/Retrieve'));
      final picturesResponse = await http.get(Uri.parse('$baseUrl/api/pictures/Retrieve'));
      final linksResponse = await http.get(Uri.parse('$baseUrl/api/links/Retrieve'));
      final pdfsResponse = await http.get(Uri.parse('$baseUrl/api/pdfs/Retrieve'));

      print('Uploads Response: ${uploadsResponse.statusCode}');
      print('Tags Response: ${tagsResponse.statusCode}');
      print('Pictures Response: ${picturesResponse.statusCode}');
      print('Links Response: ${linksResponse.statusCode}');
      print('PDFs Response: ${pdfsResponse.statusCode}');

      if (uploadsResponse.statusCode == 200 && 
          tagsResponse.statusCode == 200 && 
          picturesResponse.statusCode == 200 &&
          linksResponse.statusCode == 200 &&
          pdfsResponse.statusCode == 200) {
        
        final List<dynamic> uploads = json.decode(uploadsResponse.body)['data'];
        final List<dynamic> tags = json.decode(tagsResponse.body)['data'];
        final List<dynamic> pictures = json.decode(picturesResponse.body)['data'];
        final List<dynamic> links = json.decode(linksResponse.body)['data'];
        final List<dynamic> pdfs = json.decode(pdfsResponse.body)['data'];

        print('Found ${uploads.length} uploads');
        print('Found ${tags.length} tags');
        print('Found ${pictures.length} pictures');
        print('Found ${links.length} links');
        print('Found ${pdfs.length} pdfs');

        // Get all tags where workshop is true
        final workshopTags = tags.where((tag) => tag['workshop'] == true).toList();
        
        // Get the uploads_ids from these workshop tags
        final workshopUploadIds = workshopTags.map((tag) => tag['uploads_id'].toString()).toList();

        print('Found ${workshopTags.length} workshop tags');
        print('Workshop upload IDs: $workshopUploadIds');

        // Get all uploads that match these ids
        final filteredWorkshops = uploads.where((upload) {
          final uploadId = upload['id'].toString();
          return workshopUploadIds.contains(uploadId);
        }).map((upload) {
          // Find all pictures for this upload
          final uploadPictures = pictures.where((pic) {
            final picUploadId = pic['uploads_id'].toString();
            final uploadId = upload['id'].toString();
            return picUploadId == uploadId;
          }).toList();
          
          // Find all links for this upload
          final uploadLinks = links.where((link) {
            final linkUploadId = link['uploads_id'].toString();
            final uploadId = upload['id'].toString();
            return linkUploadId == uploadId;
          }).toList();
          
          // Find all PDFs for this upload
          final uploadPdfs = pdfs.where((pdf) {
            final pdfUploadId = pdf['uploads_id'].toString();
            final uploadId = upload['id'].toString();
            return pdfUploadId == uploadId;
          }).toList();
          
          return {
            'id': upload['id'],
            'title': upload['title'],
            'upload_date': upload['upload_date'],
            'event_date': upload['event_date'],
            'description': upload['description'],
            'location': upload['location'],
            'pictures': uploadPictures,
            'links': uploadLinks,
            'pdfs': uploadPdfs,
            'first_picture': uploadPictures.isNotEmpty ? uploadPictures[0] : null,
          };
        }).toList();

        print('Found ${filteredWorkshops.length} workshops');
        
        // Sort workshops by upload date (newest first)
        filteredWorkshops.sort((a, b) {
          DateTime dateA = DateTime.parse(a['upload_date']);
          DateTime dateB = DateTime.parse(b['upload_date']);
          return dateB.compareTo(dateA);
        });

        setState(() {
          workshops = List<Map<String, dynamic>>.from(filteredWorkshops);
          isLoading = false;
        });
      }
    } catch (e) {
      print('Error fetching workshops: $e');
      setState(() {
        isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return MainLayout(
      child: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Workshops',
                  style: TextStyle(
                    fontSize: 24,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 20),
                if (isLoading)
                  const Center(child: CircularProgressIndicator())
                else if (workshops.isEmpty)
                  const Center(child: Text('No workshops found'))
                else
                  ListView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    padding: EdgeInsets.zero,
                    itemCount: workshops.length,
                    itemBuilder: (context, index) {
                      final workshop = workshops[index];
                      return Container(
                        margin: const EdgeInsets.only(bottom: 16),
                        child: Card(
                          elevation: 2,
                          child: InkWell(
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder: (context) => DisplaycontentPage(
                                    achievement: workshop,
                                    categoryName: 'Workshop',
                                    currentIndex: index,
                                    itemsList: workshops,
                                  ),
                                ),
                              );
                            },
                            child: Padding(
                              padding: const EdgeInsets.all(12.0),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  if (workshop['first_picture'] != null)
                                    ClipRRect(
                                      borderRadius: BorderRadius.circular(8),
                                      child: CachedNetworkImage(
                                        imageUrl: '${baseUrl}/storage/${workshop['first_picture']['picture_path']}',
                                        width: double.infinity,
                                        height: 180,
                                        fit: BoxFit.cover,
                                        memCacheWidth: 400,
                                        memCacheHeight: 400,
                                        maxWidthDiskCache: 400,
                                        maxHeightDiskCache: 400,
                                        placeholder: (context, url) => Container(
                                          width: double.infinity,
                                          height: 180,
                                          color: Colors.grey[200],
                                          child: const Center(
                                            child: CircularProgressIndicator(),
                                          ),
                                        ),
                                        errorWidget: (context, url, error) => Container(
                                          width: double.infinity,
                                          height: 180,
                                          color: Colors.grey[200],
                                          child: const Icon(Icons.error_outline, color: Colors.red),
                                        ),
                                      ),
                                    ),
                                  const SizedBox(height: 12),
                                  Text(
                                    workshop['title'],
                                    style: const TextStyle(
                                      fontSize: 18,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  const SizedBox(height: 8),
                                  Text(
                                    'Uploaded on: ${workshop['upload_date']}',
                                    style: TextStyle(
                                      color: Colors.grey[600],
                                      fontSize: 14,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ),
                      );
                    },
                  ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}