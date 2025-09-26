import 'package:customer/controller/osm_search_place_controller.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/app_colors.dart';
// Google Fonts replaced with local fonts
import 'package:customer/utils/DarkThemeProvider.dart';
// Google Fonts replaced with local fonts
import 'package:flutter/material.dart';
// Google Fonts replaced with local fonts
import 'package:get/get.dart';
// Google Fonts replaced with local fonts

import 'package:provider/provider.dart';
// Google Fonts replaced with local fonts

class OsmSearchPlacesApi extends StatelessWidget {
  const OsmSearchPlacesApi({super.key});

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);
    return GetX(
        init: OsmSearchPlaceController(),
        builder: (controller) {
          return Scaffold(
            backgroundColor: Theme.of(context).colorScheme.background,
            appBar: AppBar(
              elevation: 0,
              backgroundColor: AppColors.primary,
              leading: InkWell(
                onTap: () {
                  Get.back();
                },
                child: Icon(
                  Icons.arrow_back,
                  color: themeChange.getThem()
                      ? AppColors.lightGray
                      : AppColors.lightGray,
                ),
              ),
              title: Text(
                'Search places',
                style: TextStyle(
                  color: themeChange.getThem()
                      ? AppColors.lightGray
                      : AppColors.lightGray,
                  fontSize: 16,
                ),
              ),
            ),
            body: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
              child: Column(
                children: [
                  TextFormField(
                      validator: (value) =>
                          value != null && value.isNotEmpty ? null : 'Required',
                      keyboardType: TextInputType.text,
                      textCapitalization: TextCapitalization.sentences,
                      controller: (controller as OsmSearchPlaceController).searchTxtController.value,
                      textAlign: TextAlign.start,
                      style: TextStyle(),
                      decoration: InputDecoration(
                          isDense: true,
                          filled: true,
                          fillColor: themeChange.getThem()
                              ? AppColors.darkTextField
                              : AppColors.textField,
                          contentPadding:
                              const EdgeInsets.symmetric(vertical: 12),
                          prefixIcon: const Icon(Icons.map),
                          disabledBorder: OutlineInputBorder(
                            borderRadius:
                                const BorderRadius.all(Radius.circular(4)),
                            borderSide: BorderSide(
                                color: themeChange.getThem()
                                    ? AppColors.darkTextFieldBorder
                                    : AppColors.textFieldBorder,
                                width: 1),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius:
                                const BorderRadius.all(Radius.circular(4)),
                            borderSide: BorderSide(
                                color: themeChange.getThem()
                                    ? AppColors.darkTextFieldBorder
                                    : AppColors.textFieldBorder,
                                width: 1),
                          ),
                          enabledBorder: OutlineInputBorder(
                            borderRadius:
                                const BorderRadius.all(Radius.circular(4)),
                            borderSide: BorderSide(
                                color: themeChange.getThem()
                                    ? AppColors.darkTextFieldBorder
                                    : AppColors.textFieldBorder,
                                width: 1),
                          ),
                          errorBorder: OutlineInputBorder(
                            borderRadius:
                                const BorderRadius.all(Radius.circular(4)),
                            borderSide: BorderSide(
                                color: themeChange.getThem()
                                    ? AppColors.darkTextFieldBorder
                                    : AppColors.textFieldBorder,
                                width: 1),
                          ),
                          border: OutlineInputBorder(
                            borderRadius:
                                const BorderRadius.all(Radius.circular(4)),
                            borderSide: BorderSide(
                                color: themeChange.getThem()
                                    ? AppColors.darkTextFieldBorder
                                    : AppColors.textFieldBorder,
                                width: 1),
                          ),
                          suffixIcon: IconButton(
                            icon: const Icon(Icons.cancel),
                            onPressed: () {
                              (controller as OsmSearchPlaceController).searchTxtController.value.clear();
                            },
                          ),
                          hintText: "Search your location here".tr)),
                  Expanded(
                    child: ListView.builder(
                      shrinkWrap: true,
                      primary: true,
                      itemCount: (controller as OsmSearchPlaceController).suggestionsList.length,
                      itemBuilder: (context, index) {
                        return ListTile(
                          title: Text(
                            (controller as OsmSearchPlaceController).suggestionsList[index].address
                                .toString(),
                            style: TextStyle(),
                          ),
                          onTap: () {
                            Get.back(result: (controller as OsmSearchPlaceController).suggestionsList[index]);
                          },
                        );
                      },
                    ),
                  ),
                ],
              ),
            ),
          );
        });
  }
}
