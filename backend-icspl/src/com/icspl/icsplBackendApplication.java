package backend-icspl.src.com.icspl

package com.icspl;

import jakarta.persistence.*;
import jakarta.validation.constraints.*;
import lombok.Data;
import lombok.RequiredArgsConstructor;
import org.hibernate.annotations.CreationTimestamp;
import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.boot.autoconfigure.domain.EntityScan;
import org.springframework.boot.CommandLineRunner;
import org.springframework.context.annotation.Bean;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.config.EnableJpaRepositories;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Repository;
import org.springframework.stereotype.Service;
import org.springframework.validation.annotation.Validated;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.config.annotation.CorsRegistry;
import org.springframework.web.servlet.config.annotation.WebMvcConfigurer;
import org.modelmapper.ModelMapper;

import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.*;
import java.util.stream.Collectors;

@SpringBootApplication
@EntityScan(basePackages = "com.icspl")
@EnableJpaRepositories(basePackages = "com.icspl")
public class IcsplBackendApplication {

    public static void main(String[] args) {
        SpringApplication.run(IcsplBackendApplication.class, args);
    }

    @Bean
    public WebMvcConfigurer corsConfigurer() {
        return new WebMvcConfigurer() {
            @Override
            public void addCorsMappings(CorsRegistry registry) {
                registry.addMapping("/**")
                        .allowedOrigins("http://localhost:5500", "http://127.0.0.1:5500")
                        .allowedMethods("*")
                        .allowedHeaders("*");
            }
        };
    }

    @Bean
    public ModelMapper modelMapper() {
        return new ModelMapper();
    }

    @Bean
    CommandLineRunner initDatabase(WebsiteContentRepository contentRepo, 
                                 ServiceRepository serviceRepo,
                                 SectorRepository sectorRepo) {
        return args -> {
            // Initialize website content
            contentRepo.saveAll(List.of(
                new WebsiteContent("home", "hero_title", "Welcome to ICSPL"),
                new WebsiteContent("home", "hero_subtitle", "Innovative Solutions for Your Business"),
                new WebsiteContent("about", "mission", "Our mission is to deliver cutting-edge technology solutions..."),
                new WebsiteContent("services", "header", "Our Comprehensive Services"),
                new WebsiteContent("sectors", "header", "Industries We Serve")
            ));

            // Initialize services
            serviceRepo.saveAll(List.of(
                new Service("IT Consulting", "Comprehensive IT consulting services", "fas fa-laptop-code", true),
                new Service("Cloud Solutions", "Scalable cloud solutions", "fas fa-cloud", true),
                new Service("Cybersecurity", "Protection from digital threats", "fas fa-shield-alt", false)
            ));

            // Initialize sectors
            sectorRepo.saveAll(List.of(
                new Sector("Healthcare", "Solutions for healthcare providers", "/images/healthcare.jpg"),
                new Sector("Finance", "Secure financial technology", "/images/finance.jpg"),
                new Sector("Education", "Technology for education", "/images/education.jpg")
            ));
        };
    }
}

// ========== ENTITIES ==========
@Entity
@Data
class ContactMessage {
    @Id @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    private String name;
    private String email;
    private String message;
    @CreationTimestamp
    private LocalDateTime createdAt;
    private String status = "NEW";
}

@Entity
@Data
class Service {
    @Id @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    private String title;
    private String description;
    private String iconClass;
    private boolean featured;
}

@Entity
@Data
class Sector {
    @Id @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    private String name;
    private String description;
    private String imageUrl;
}

@Entity
@Data
@Table(uniqueConstraints = @UniqueConstraint(columnNames = {"pageName", "sectionName"}))
class WebsiteContent {
    @Id @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    private String pageName;
    private String sectionName;
    private String content;
}

// ========== REPOSITORIES ==========
@Repository
interface ContactRepository extends JpaRepository<ContactMessage, Long> {}

@Repository
interface ServiceRepository extends JpaRepository<Service, Long> {
    List<Service> findByFeatured(boolean featured);
}

@Repository
interface SectorRepository extends JpaRepository<Sector, Long> {}

@Repository
interface WebsiteContentRepository extends JpaRepository<WebsiteContent, Long> {
    List<WebsiteContent> findByPageName(String pageName);
    WebsiteContent findByPageNameAndSectionName(String pageName, String sectionName);
}

// ========== DTOs ==========
@Data
class ContactRequestDto {
    @NotBlank(message = "Name is required")
    @Size(max = 100, message = "Name must be less than 100 characters")
    private String name;
    
    @NotBlank(message = "Email is required")
    @Email(message = "Email should be valid")
    @Size(max = 100, message = "Email must be less than 100 characters")
    private String email;
    
    @NotBlank(message = "Message is required")
    @Size(max = 2000, message = "Message must be less than 2000 characters")
    private String message;
}

@Data
class ContactResponseDto {
    private Long id;
    private String name;
    private String email;
    private String message;
    private String createdAt;
    private String status;
    private String responseMessage;
}

@Data
class ServiceDto {
    private Long id;
    private String title;
    private String description;
    private String iconClass;
}

@Data
class SectorDto {
    private Long id;
    private String name;
    private String description;
    private String imageUrl;
}

// ========== SERVICES ==========
@Service
@Validated
class ContactService {
    private final ContactRepository contactRepository;
    private final ModelMapper modelMapper;

    public ContactService(ContactRepository contactRepository, ModelMapper modelMapper) {
        this.contactRepository = contactRepository;
        this.modelMapper = modelMapper;
    }

    public ContactResponseDto saveContactMessage(ContactRequestDto contactRequestDto) {
        ContactMessage message = modelMapper.map(contactRequestDto, ContactMessage.class);
        ContactMessage savedMessage = contactRepository.save(message);
        
        ContactResponseDto response = modelMapper.map(savedMessage, ContactResponseDto.class);
        response.setCreatedAt(savedMessage.getCreatedAt()
                .format(DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss")));
        response.setStatus(savedMessage.getStatus());
        response.setResponseMessage("Thank you for contacting us! We'll get back to you soon.");
        
        return response;
    }
}

@Service
class WebsiteService {
    private final WebsiteContentRepository contentRepository;
    private final ServiceRepository serviceRepository;
    private final SectorRepository sectorRepository;
    private final ModelMapper modelMapper;

    public WebsiteService(WebsiteContentRepository contentRepository, 
                        ServiceRepository serviceRepository,
                        SectorRepository sectorRepository,
                        ModelMapper modelMapper) {
        this.contentRepository = contentRepository;
        this.serviceRepository = serviceRepository;
        this.sectorRepository = sectorRepository;
        this.modelMapper = modelMapper;
    }

    public Map<String, String> getPageContent(String pageName) {
        return contentRepository.findByPageName(pageName).stream()
                .collect(Collectors.toMap(
                        WebsiteContent::getSectionName,
                        WebsiteContent::getContent));
    }

    public List<ServiceDto> getFeaturedServices() {
        return serviceRepository.findByFeatured(true).stream()
                .map(service -> modelMapper.map(service, ServiceDto.class))
                .collect(Collectors.toList());
    }

    public List<SectorDto> getAllSectors() {
        return sectorRepository.findAll().stream()
                .map(sector -> modelMapper.map(sector, SectorDto.class))
                .collect(Collectors.toList());
    }
}

// ========== CONTROLLERS ==========
@RestController
@RequestMapping("/api/contact")
@RequiredArgsConstructor
class ContactController {
    private final ContactService contactService;

    @PostMapping
    public ResponseEntity<ContactResponseDto> submitContactForm(
            @Valid @RequestBody ContactRequestDto contactRequestDto) {
        ContactResponseDto response = contactService.saveContactMessage(contactRequestDto);
        return ResponseEntity.ok(response);
    }
}

@RestController
@RequestMapping("/api/content")
class WebsiteController {
    private final WebsiteService websiteService;

    @GetMapping("/home")
    public Map<String, Object> getHomeContent() {
        Map<String, Object> response = new HashMap<>();
        response.put("content", websiteService.getPageContent("home"));
        response.put("services", websiteService.getFeaturedServices());
        return response;
    }

    @GetMapping("/about")
    public Map<String, String> getAboutContent() {
        return websiteService.getPageContent("about");
    }

    @GetMapping("/services")
    public Map<String, Object> getServicesContent() {
        Map<String, Object> response = new HashMap<>();
        response.put("content", websiteService.getPageContent("services"));
        response.put("allServices", websiteService.getFeaturedServices());
        return response;
    }

    @GetMapping("/sectors")
    public Map<String, Object> getSectorsContent() {
        Map<String, Object> response = new HashMap<>();
        response.put("content", websiteService.getPageContent("sectors"));
        response.put("sectors", websiteService.getAllSectors());
        return response;
    }
}